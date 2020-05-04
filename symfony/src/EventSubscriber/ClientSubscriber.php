<?php


namespace App\EventSubscriber;


use App\Entity\Client;
use App\Entity\Provider;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class ClientSubscriber implements EventSubscriber
{

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $client = $args->getObject();

        if(!$client instanceof Client){
            return;
        }

        if($client->getReference()){
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Client::class)->findBy([
            'company' => $client->getCompany()
        ]));

        $client->setReference(str_pad($count, 10, '0', STR_PAD_LEFT));
    }


}