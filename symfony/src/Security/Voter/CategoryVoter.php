<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use App\Security\AbstractUserRoles;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CategoryVoter extends AbstractVoter
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
     * @return bool
     * @return bool
     */
    protected function supports($attribute, $subject): bool
    {
        if (!$subject instanceof Category) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param User|null $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        if (!$subject instanceof Category) {
            return false;
        }

        /** @var User $user */
        $user = $token->getUser();

        if ($this->isRoleGod($user->getRoles())) {
            return true;
        }

        if($this->isRoleAdmin($user->getRoles(),$user,$subject,$attribute)){
            return true;
        }

        if ($attribute === parent::DELETE && in_array(User::ROLE_DELETE_PRODUCT,$user->getRoles(),true)){
            return true;
        }

        if ($attribute === parent::READ && in_array(User::ROLE_READ_PRODUCT,$user->getRoles(),true)){
            return true;
        }

        if ($attribute === parent::CREATE && in_array(User::ROLE_CREATE_PRODUCT,$user->getRoles(),true)){
            return true;
        }

        if ($attribute === parent::UPDATE && in_array(User::ROLE_UPDATE_PRODUCT,$user->getRoles(),true)){
            return true;
        }

        return false;
    }
}
