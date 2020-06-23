<?php

namespace App\Security\Voter;

use App\Entity\Client;
use App\Entity\ProductMediaObject;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class ImagesVoter extends AbstractVoter
{
    /** @var Security */
    protected $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param $attribute string
     * @param $subject User
     */
    protected function supports($attribute, $subject): bool
    {
        if (!$subject instanceof ProductMediaObject) {
            return false;
        }

        return true;
    }

    /**
     * @param string    $attribute
     * @param User|null $subject
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof ProductMediaObject) {
            return false;
        }

        /** @var User $user */
        $user = $token->getUser();

        if ($this->isRoleGod($user->getRoles())) {
            return true;
        }

        if (self::UPDATE === $attribute && in_array(User::ROLE_ADMIN, $user->getRoles(), true) &&  $subject->getCompany() === $user->getCompany()) {
            return true;
        }

        if (parent::DELETE === $attribute && in_array(User::ROLE_DELETE_PRODUCT, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
