<?php

namespace App\Repository;

use App\Entity\Opening;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Opening|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opening|null findOneBy(array $criteria, array $orderBy = null)
 * @method Opening[]    findAll()
 * @method Opening[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpeningRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Opening::class);
    }

    // /**
    //  * @return Opening[] Returns an array of Opening objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('o.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Opening
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function findOneByDates($open, $close)
    {
        return $this->createQueryBuilder('o')
                ->andWhere('o.open = :open')
                ->andWhere('o.close = :close')
                ->setParameter('open', $open)
                ->setParameter('close', $close)
                ->getQuery()
                ->getResult()
            ;
    }
}
