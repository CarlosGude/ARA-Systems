<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\ProductProvider;
use App\Entity\Purchase;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;

class PurchaseSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::onFlush,
            Events::prePersist,
            Events::preUpdate
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $purchase = $args->getObject();

        if (!$purchase instanceof Purchase) {
            return;
        }

        if ($purchase->getReference()) {
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Purchase::class)->findBy([
            'company' => $purchase->getCompany(),
        ]));

        $purchase->setReference(str_pad($count, 10, '0', STR_PAD_LEFT));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $purchase = $args->getObject();

        if (!$purchase instanceof Purchase) {
            return;
        }

        if (!$args->hasChangedField('status')){
            return;
        }

        if (!$args->getNewValue('status') === Purchase::STATUS_CANCELLED){
            return;
        }

        $provider = $purchase->getProvider();

        if($purchase->getPurchaseLines()->count() === 0){
            return;
        }

        foreach ($purchase->getPurchaseLines() as $purchaseLine){


        }
    }

    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Purchase) {
                return;
            }

            if (Purchase::STATUS_SUCCESS === $entity->getStatus()) {
                $provider = $entity->getProvider();
                foreach ($entity->getPurchaseLines() as $line) {
                    $product = $line->getProduct();
                    if (!$product) {
                        throw new  RuntimeException('product: This value should not be blank.');
                    }

                    $product->setStockAct($line->getQuantity() + $product->getStockAct());
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Product::class), $product);

                    $productProviders = $product
                        ->getProductProviders()
                        ->filter(static function(ProductProvider $productProvider) use ($provider){
                            return $productProvider->getProvider() === $provider;
                        });

                    if($productProviders->count() === 0){
                        $productProvider = new ProductProvider();
                        $productProvider
                            ->setPrice($line->getPrice())
                            ->setProvider($provider)
                            ->setProduct($product)
                            ->setCompany($entity->getCompany())
                        ;

                        $em->persist($productProvider);
                        $uow->computeChangeSet(
                            $em->getClassMetadata(ProductProvider::class),
                            $productProvider
                        );

                    }else{
                        /** @var ProductProvider $productProvider */
                        $productProvider = $productProviders->first();
                        if ($productProvider->getPrice() === $line->getPrice()){
                            return;
                        }

                        $productProvider->setPrice($line->getPrice());

                        $uow->recomputeSingleEntityChangeSet(
                            $em->getClassMetadata(ProductProvider::class),
                            $productProvider
                        );
                    }
                }
            }
        }
    }
}
