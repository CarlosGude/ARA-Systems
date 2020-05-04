<?php


namespace App\EventSubscriber;


use App\Entity\Provider;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ProviderSubscriber implements EventSubscriber
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $provider = $args->getObject();

        if(!$provider instanceof Provider){
            return;
        }

        if($provider->getReference()){
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Provider::class)->findBy([
            'company' => $provider->getCompany()
        ]));

        $provider->setReference(str_pad($count, 10, '0', STR_PAD_LEFT));
    }


}