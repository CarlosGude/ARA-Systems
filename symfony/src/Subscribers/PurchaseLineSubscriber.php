<?php


namespace App\Subscribers;


use App\Entity\Product;
use App\Entity\Provider;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;

class PurchaseLineSubscriber implements EventSubscriber
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preRemove,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $line = $args->getObject();

        if (!$line instanceof PurchaseLine) {
            return;
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
                });

        if ($concurrences->count() >= 1) {
            /** @var PurchaseLine $concurrence */
            foreach ($concurrences as $concurrence) {
                $line->setQuantity($concurrence->getQuantity() + $line->getQuantity());
                $args->getObjectManager()->remove($concurrence);
            }
        }

        $line
            ->setPrice($product->getPrice())
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

        $line
            ->setPrice($product->getPrice())
            ->setTax($product->getTax());

        $purchase
            ->setTotal($purchase->getTotal() - ($line->getPrice() * $line->getQuantity()))
            ->setTaxes($purchase->getTaxes() - ($line->getPrice() * $line->getQuantity()) * ($line->getTax() / 100));

    }

    public function preUpdate(PreUpdateEventArgs $args)
    {
        $line = $args->getObject();

        if (!$line instanceof PurchaseLine) {
            return;
        }

        $purchase = $line->getPurchase();

        if ($args->hasChangedField('product')) {
            throw new  RuntimeException('product: This value should not be updated.');
        }

        if ($args->hasChangedField('quantity')) {
            $oldQuantity = $args->getOldValue('quantity');
            $newQuantity = $args->getNewValue('quantity');

            if ($newQuantity > $oldQuantity) {
                $diff = $newQuantity - $oldQuantity;
                $purchase
                    ->setTotal($purchase->getTotal() + ($line->getPrice() * $diff))
                    ->setTaxes($purchase->getTaxes() + ($line->getPrice() * $diff) * ($line->getTax() / 100));
            } else {
                $diff = $oldQuantity - $newQuantity;
                $purchase
                    ->setTotal($purchase->getTotal() - ($line->getPrice() * $diff))
                    ->setTaxes($purchase->getTaxes() - ($line->getPrice() * $diff) * ($line->getTax() / 100));
            }
        }
    }


}