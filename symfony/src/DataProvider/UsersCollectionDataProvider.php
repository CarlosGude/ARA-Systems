<?php


namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class UsersCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var Security
     */
    private $security;
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    public function __construct(Security $security, ManagerRegistry $managerRegistry)
    {
        $this->security = $security;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        return $this->managerRegistry->getRepository(User::class)->findBy([
            'company' => $user->getCompany()
        ]);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        if ($resourceClass !== User::class) {
            return false;
        }

        if ($operationName !== 'get') {
            return false;
        }

        return true;
    }
}
