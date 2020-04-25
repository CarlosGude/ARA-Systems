<?php


namespace App\Security\Voter;

use App\Entity\User;
use App\Interfaces\EntityInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    protected const CREATE = 'CREATE';
    protected const UPDATE = 'UPDATE';
    protected const DELETE = 'DELETE';
    protected const READ = 'READ';

    protected function isRoleGod(array $roles): bool
    {
        return in_array(User::ROLE_GOD, $roles, true);
    }

    protected function isRoleAdmin(array $roles,User $user, $entity, string $attribute): bool
    {
        if (!$entity instanceof EntityInterface && !$entity instanceof User)
        {
            return false;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            if($attribute === self::READ){
                return true;
            }

            if ($attribute === self::CREATE) {
                return true;
            }

            if ($attribute===self::UPDATE && $entity->getCompany() === $user->getCompany()){
                return true;
            }

            if ($attribute===self::DELETE && $entity->getCompany() === $user->getCompany()){
                return true;
            }
        }

        return false;
    }
}