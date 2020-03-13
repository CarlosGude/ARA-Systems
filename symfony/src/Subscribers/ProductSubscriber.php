<?php


namespace App\Subscribers;

use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ProductSubscriber implements EventSubscriber
{
    /**
     * @inheritDoc
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $product = $args->getObject();

        if (!$product instanceof Product) {
            return;
        }

        /** The products inherit some values from the category */
        $product
            ->setIva($product->getCategory()->getIva())
            ->setMinStock($product->getCategory()->getMinStock())
            ->setMaxStock($product->getCategory()->getMaxStock());
    }
}
