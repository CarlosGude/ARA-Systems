<?php

namespace App\Subscribers;

use App\Entity\Category;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;

class CategorySubscriber implements EventSubscriber
{
    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents(): array
    {
        return [
            Events::preRemove,
        ];
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $category = $args->getObject();

        if (!$category instanceof Category) {
            return;
        }

        if ($category->getProducts()->count() > 0) {
            throw new RuntimeException('The category '.$category->getName().' can not be deleted because it has products nested.', 400);
        }
    }
}
