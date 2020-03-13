<?php

namespace App\Security\Voter;

use App\Entity\Category;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CategoryVoter extends Voter
{
    protected const CREATE = 'CREATED';
    protected const UPDATE = 'UPDATE';
    protected const DELETE = 'DELETE';
    protected const READ = 'READ';
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

        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException(
                'Need to login'
            );
        }

        if (in_array(User::ROLE_GOD, $user->getRoles(), true)) {
            return true;
        }

        if ($subject->getCompany() === $user->getCompany()) {
            return true;
        }

        return false;
    }

}
