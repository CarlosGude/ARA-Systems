<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CompanyVoter extends AbstractVoter
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
        if (!$subject instanceof Company) {
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
        if (!$subject instanceof Company) {
            return false;
        }

        /** @var User $user */
        $user = $token->getUser();

        if ($this->isRoleGod($user->getRoles())) {
            return true;
        }

        if (self::UPDATE === $attribute && in_array(User::ROLE_ADMIN, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
