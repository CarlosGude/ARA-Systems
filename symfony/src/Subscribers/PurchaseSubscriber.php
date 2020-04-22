<?php

namespace App\Subscribers;

use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\OnFlushEventArgs;
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
        ];
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs): void
    {
        $em = $eventArgs->getEntityManager();
        $uow = $em->getUnitOfWork();

        foreach ($uow->getScheduledEntityUpdates() as $entity) {
            if (!$entity instanceof Purchase) {
                return;
            }

            if($entity->getStatus() === Purchase::STATUS_SUCCESS){
                foreach ($entity->getPurchaseLines() as $line){
                    $product = $line->getProduct();
                    if(!$product){
                        throw new  RuntimeException('product: This value should not be blank.');
                    }

                    $product->setStockAct($line->getQuantity() + $product->getStockAct());
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Product::class), $product);
                }
            }
        }
    }
}
