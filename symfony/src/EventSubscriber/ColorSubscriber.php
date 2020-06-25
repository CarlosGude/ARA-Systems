<?php

namespace App\EventSubscriber;

use App\Entity\Client;
use App\Entity\Color;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ColorSubscriber implements EventSubscriber
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

        if (!$color instanceof Color) {
            return;
        }

        if ($color->getReference()) {
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Color::class)->findBy([
            'company' => $color->getCompany(),
        ]));

        $color->setReference(str_pad($count, 2, '0', STR_PAD_LEFT));
    }
}
