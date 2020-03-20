<?php


namespace App\DataProvider;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\PaginationExtension;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryResultCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGenerator;
use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\Company;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Security;

class CustomDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{


    /**
     * @var Security
     */
    private $security;
    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;
    /**
     * @var PaginationExtension
     */
    private $paginationExtension;
    /**
     * @var array
     */
    private $context;

    public function __construct(Security $security, ManagerRegistry $managerRegistry, PaginationExtension $paginationExtension)
    {
        $this->security = $security;
        $this->managerRegistry = $managerRegistry;
        $this->paginationExtension = $paginationExtension;
    }

    /**
     * @inheritDoc
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $queryBuilder = $this->managerRegistry
            ->getManagerForClass($resourceClass)
            ->getRepository($resourceClass)->createQueryBuilder('u')
            ->where('u.company = :company')
            ->setParameter('company', $user->getCompany());


        $this->paginationExtension->applyToCollection($queryBuilder, new QueryNameGenerator(), $resourceClass, $operationName, $this->context);

        if ($this->paginationExtension instanceof QueryResultCollectionExtensionInterface
            && $this->paginationExtension->supportsResult($resourceClass, $operationName, $this->context)) {

            return $this->paginationExtension->getResult($queryBuilder, $resourceClass, $operationName, $this->context);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        if ($resourceClass === Company::class) {
            return false;
        }

        if ($operationName !== 'get') {
            return false;
        }
        $this->context = $context;
        return true;
    }
}
