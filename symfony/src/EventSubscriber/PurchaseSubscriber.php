<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Entity\Purchase;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
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
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $purchase = $args->getObject();

        if(!$purchase instanceof Purchase){
            return;
        }

        if($purchase->getReference()){
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Purchase::class)->findBy([
            'company' => $purchase->getCompany()
        ]));

        $purchase->setReference(str_pad($count, 10, '0', STR_PAD_LEFT));
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
                foreach ($entity->getPurchaseLines() as $line) {
                    $product = $line->getProduct();
                    if (!$product) {
                        throw new  RuntimeException('product: This value should not be blank.');
                    }

                    $product->setStockAct($line->getQuantity() + $product->getStockAct());
                    $uow->recomputeSingleEntityChangeSet($em->getClassMetadata(Product::class), $product);
                }
            }
        }
    }
}
