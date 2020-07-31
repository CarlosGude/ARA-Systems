<?php

namespace App\EventSubscriber;

use App\Entity\Size;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class SizeSubscriber implements EventSubscriber
{
    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $color = $args->getObject();

        if (!$color instanceof Size) {
            return;
        }

        if ($color->getReference()) {
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Size::class)->findBy([
            'company' => $color->getCompany(),
        ]));

        $color->setReference(str_pad($count, 2, '0', STR_PAD_LEFT));
    }
}
