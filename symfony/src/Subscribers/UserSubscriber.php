<?php

namespace App\Subscribers;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserSubscriber implements EventSubscriber
{
    /** @var UserPasswordEncoderInterface */
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
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
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        $encoded = $this->encoder->encodePassword($user, $user->getPassword());

        $user->setPassword($encoded)->setRoles($this->getRoles($user->getProfile()));
    }

    public function preUpdate(PreUpdateEventArgs $args): void
    {
        $user = $args->getObject();

        if (!$user instanceof User) {
            return;
        }

        if ($args->hasChangedField('password')) {
            $encoded = $this->encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($encoded);
        }

        if ($args->hasChangedField('profile')) {
            $user->setRoles($this->getRoles($user->getProfile()));
        }
    }

    public function getRoles($profile): array
    {
        switch ($profile){
            case User::PROFILE_GOD:
                return [User::ROLE_GOD];
            case User::PROFILE_ADMIN;
                return [User::ROLE_ADMIN];
            case User::PROFILE_PURCHASER:
                return [
                    User::ROLE_CREATE_CATEGORY,
                    User::ROLE_READ_CATEGORY,
                    User::ROLE_UPDATE_CATEGORY,

                    User::ROLE_READ_PRODUCT,
                    User::ROLE_CREATE_PRODUCT,
                    User::ROLE_UPDATE_PRODUCT,

                    User::ROLE_UPDATE_PROVIDER,
                    User::ROLE_CREATE_PROVIDER,
                    User::ROLE_READ_PROVIDER,

                    User::ROLE_READ_PURCHASE,
                    User::ROLE_CREATE_PURCHASE,
                    User::ROLE_UPDATE_PURCHASE,

                    User::ROLE_DELETE_PURCHASE_LINE,
                    User::ROLE_READ_PURCHASE_LINE,
                    User::ROLE_CREATE_PURCHASE_LINE,
                    User::ROLE_UPDATE_PURCHASE_LINE,
                ];
            case User::PROFILE_SELLER:
                return [
                    User::ROLE_CREATE_CATEGORY,
                    User::ROLE_READ_CATEGORY,
                    User::ROLE_UPDATE_CATEGORY,

                    User::ROLE_READ_PRODUCT,
                    User::ROLE_CREATE_PRODUCT,
                    User::ROLE_UPDATE_PRODUCT,

                    User::ROLE_CREATE_CLIENT,
                    User::ROLE_READ_CLIENT,
                    User::ROLE_UPDATE_CLIENT,

                ];
        }
    }
}
