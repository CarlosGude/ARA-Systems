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

        return ($this->isRoleGod($user->getRoles())) || $this->isRoleAdmin($user->getRoles(), $user, $subject, $attribute);
    }
}
