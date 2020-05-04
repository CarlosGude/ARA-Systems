<?php

namespace App\Repository;

use App\Entity\ProductMediaObject;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method ProductMediaObject|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProductMediaObject|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProductMediaObject[]    findAll()
 * @method ProductMediaObject[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductMediaObjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductMediaObject::class);
    }

    // /**
    //  * @return ProductMediaObject[] Returns an array of ProductMediaObject objects
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
    public function findOneBySomeField($value): ?ProductMediaObject
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
