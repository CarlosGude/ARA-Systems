<?php

namespace App\Repository;

use App\Entity\ProductProvider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductProvider|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductProvider|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductProvider[]    findAll()
 * @method ProductProvider[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductProvider::class);
    }

    // /**
    //  * @return ProductProvider[] Returns an array of ProductProvider objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ProductProvider
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
