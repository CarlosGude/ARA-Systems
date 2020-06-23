<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\ProductProvider;
use App\Entity\Provider;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\ORMException;
use RuntimeException;

class PurchaseLineSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preRemove,
            Events::preUpdate,
            Events::onFlush,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $line = $args->getObject();

        if (!$line instanceof PurchaseLine) {
            return;
        }

        if (Purchase::STATUS_INCOMING === $line->getPurchase()->getStatus()) {
            throw new  RuntimeException('purchase: A line can not be added to purchase to a purchase with status incoming.');
        }

        if (Purchase::STATUS_CANCELLED === $line->getPurchase()->getStatus()) {
            throw new  RuntimeException('purchase: A line can not be added to purchase to a purchase with status cancelled.');
        }

        $product = $line->getProduct();
        $purchase = $line->getPurchase();
        $provider = $line->getProvider();

        if (!$product instanceof Product) {
            throw new  RuntimeException('product: This value should not be blank.');
        }

        if (!$purchase instanceof Purchase) {
            throw new  RuntimeException('purchase: This value should not be blank.');
        }

        if (!$provider instanceof Provider) {
            throw new  RuntimeException('provider: This value should not be blank.');
        }

        $concurrences = $purchase->getPurchaseLines()
            ->filter(
                static function (PurchaseLine $purchaseLine) use ($product) {
                    return $product === $purchaseLine->getProduct();
                }
            );

        if ($concurrences->count() >= 1) {
            /** @var PurchaseLine $concurrence */
            foreach ($concurrences as $concurrence) {
                $line->setQuantity($concurrence->getQuantity() + $line->getQuantity());
                $args->getObjectManager()->remove($concurrence);
            }
        }

        $price = 0;
        $productProviders = $product
            ->getProductProviders()
            ->filter(static function (ProductProvider $productProvider) use ($provider){
                return $productProvider->getProvider() === $provider;
            });

        if ($productProviders->count() === 1){
            /** @var ProductProvider $productProvider */
            $productProvider = $productProviders->first();
            $price = $productProvider->getPrice();
        }

        $line
            ->setProvider($purchase->getProvider())
            ->setPrice($price)
            ->setTax($product->getTax());

        $purchase
            ->setTotal($purchase->getTotal() + ($line->getPrice() * $line->getQuantity()))
            ->setTaxes($purchase->getTaxes() + ($line->getPrice() * $line->getQuantity()) * ($line->getTax() / 100));
    }

    public function preRemove(LifecycleEventArgs $args): void
    {
        $line = $args->getObject();

        if (!$line instanceof PurchaseLine) {
            return;
        }
        $purchase = $line->getPurchase();

        $purchase
            ->setTotal($purchase->getTotal() - ($line->getPrice() * $line->getQuantity()))
            ->setTaxes($purchase->getTaxes() - ($line->getPrice() * $line->getQuantity()) * ($line->getTax() / 100));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $line = $args->getObject();

        if (!$line instanceof PurchaseLine) {
            return;
        }

        if ($args->hasChangedField('product')) {
            throw new  RuntimeException('product: This value should not be updated.');
        }
    }

    /**
     * @throws ORMException
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof PurchaseLine) {
                return;
            }

            $purchase = $entity->getPurchase();

            foreach ($purchase->getPurchaseLines() as $line) {
                if (0 === $line->getQuantity()) {
                    $em->remove($line);
                }
            }

            $purchase->updatePrice();
            $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Purchase::class), $purchase);
        }
    }
}
