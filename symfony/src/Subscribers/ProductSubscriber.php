<?php

namespace App\Subscribers;

use App\Entity\Product;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;

class ProductSubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $product = $args->getObject();

        if (!$product instanceof Product) {
            return;
        }

        if ($product->getCompany() !== $product->getCategory()->getCompany()) {
            throw new RuntimeException('The category of product must be owned by the company');
        }

        /* The products inherit some values from the category */
        $product
            ->setTax($product->getCategory()->getTax())
            ->setMinStock($product->getCategory()->getMinStock())
            ->setMaxStock($product->getCategory()->getMaxStock());
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $product = $args->getObject();

        if (!$product instanceof Product) {
            return;
        }

        if ($product->getCompany() !== $product->getCategory()->getCompany()) {
            throw new RuntimeException('The category of product must be owned by the company');
        }
    }
}
