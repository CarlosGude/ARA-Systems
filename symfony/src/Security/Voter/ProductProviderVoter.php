<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\ProductProvider;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;

class ProductProviderVoter extends AbstractVoter
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
        if (!$subject instanceof ProductProvider) {
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

        if (!$subject instanceof ProductProvider) {
            return false;
        }

        $user = $token->getUser();

        if (!$user instanceof User) {
            throw new UnauthorizedHttpException('Need to login');
        }

        if ($this->isRoleGod($user->getRoles())) {
            return true;
        }

        if ($this->isRoleAdmin($user->getRoles(), $user, $subject, $attribute)) {
            return true;
        }

        if (parent::DELETE === $attribute && in_array(User::ROLE_DELETE_PRODUCT_PROVIDER, $user->getRoles(), true)) {
            return true;
        }

        if (parent::READ === $attribute && in_array(User::ROLE_READ_PRODUCT_PROVIDER, $user->getRoles(), true)) {
            return true;
        }

        if (parent::CREATE === $attribute && in_array(User::ROLE_CREATE_PRODUCT_PROVIDER, $user->getRoles(), true)) {
            return true;
        }

        if (parent::UPDATE === $attribute && in_array(User::ROLE_UPDATE_PRODUCT_PROVIDER, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
