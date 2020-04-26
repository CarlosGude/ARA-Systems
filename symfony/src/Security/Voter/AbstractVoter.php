<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Interfaces\EntityInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    public const CREATE = 'CREATE';
    public const UPDATE = 'UPDATE';
    public const DELETE = 'DELETE';
    public const READ = 'READ';

    protected function isRoleGod(array $roles): bool
    {
        return in_array(User::ROLE_GOD, $roles, true);
    }

    protected function isRoleAdmin(array $roles, User $user, $entity, string $attribute): bool
    {
        if (!$entity instanceof EntityInterface && !$entity instanceof User) {
            return false;
        }

        if (in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            if (self::READ === $attribute) {
                return true;
            }

            if (self::CREATE === $attribute) {
                return true;
            }

            if (self::UPDATE === $attribute && $entity->getCompany() === $user->getCompany()) {
                return true;
            }

            if (self::DELETE === $attribute && $entity->getCompany() === $user->getCompany()) {
                return true;
            }
        }

        return false;
    }
}
