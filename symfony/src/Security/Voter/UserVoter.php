<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class UserVoter extends AbstractVoter
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
        if (!$subject instanceof User) {
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
        if (!$subject instanceof User) {
            return false;
        }
        /** @var User $user */
        $user = $token->getUser();

        if ($this->isRoleGod($user->getRoles())) {
            return true;
        }

        if ($this->isRoleAdmin($user->getRoles(), $user, $subject, $attribute)) {
            return true;
        }

        if (parent::DELETE === $attribute && in_array(User::ROLE_DELETE_USER, $user->getRoles(), true)) {
            return true;
        }

        if (parent::READ === $attribute && in_array(User::ROLE_READ_USER, $user->getRoles(), true)) {
            return true;
        }

        if (parent::CREATE === $attribute && in_array(User::ROLE_CREATE_USER, $user->getRoles(), true)) {
            return true;
        }

        if (parent::UPDATE === $attribute && in_array(User::ROLE_UPDATE_USER, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
