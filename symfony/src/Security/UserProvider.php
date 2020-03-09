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
     * @inheritDoc
     */
    public function upgradePassword(UserInterface $user, string $newEncodedPassword): void
    {
        $user->setPassword($newEncodedPassword);

        $this->em->getManager()->flush();
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException('Instances of "' . get_class($user) . '" are not supported.');
        }

        return $this->loadUserByUsername($user->getEmail());
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername(string $username): User
    {
        $user = $this->em->getRepository(User::class)->findOneBy(['email' => $username]);

        if (!$user) {
            throw new UsernameNotFoundException('The email "' . $username . '" has not found.');
        }

        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass(string $class)
    {
        return $class === User::class;
    }
}
