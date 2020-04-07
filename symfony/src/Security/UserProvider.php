<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    /** @var ManagerRegistry */
    protected $em;

    public function __construct(ManagerRegistry $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        $user->setPassword($newEncodedPassword);

        $this->em->getManager()->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException('Instances of "'.get_class($user).'" are not supported.');
        }

        return $this->loadUserByUsername($user->getEmail());
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername(string $username): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $username]);

        if (!$user) {
            throw new UsernameNotFoundException('The email "'.$username.'" has not found.');
        }

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsClass(string $class)
    {
        return User::class === $class;
    }
}
