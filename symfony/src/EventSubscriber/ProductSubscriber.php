<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use App\Security\AbstractUserRoles;
use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use RuntimeException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\UsageTrackingTokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class ProductSubscriber implements EventSubscriber
{
    /**
     * @var UsageTrackingTokenStorage
     */
    private $token;

    public function __construct(UsageTrackingTokenStorage $token)
    {
        $this->token = $token;
    }

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

        /* The products inherit some values from the category */
        $product
            ->setTax($product->getCategory()->getTax())
            ->setMinStock($product->getCategory()->getMinStock())
            ->setMaxStock($product->getCategory()->getMaxStock());

        if ($product->getReference()) {
            return;
        }

        $count = count($args->getObjectManager()->getRepository(Product::class)->findBy([
            'company' => $product->getCompany(),
        ]));

        $product->setReference(str_pad($count, 10, '0', STR_PAD_LEFT));
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
