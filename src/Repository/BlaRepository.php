<?php

namespace App\Repository;

use App\Entity\Bla;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Bla|null find($id, $lockMode = null, $lockVersion = null)
 * @method Bla|null findOneBy(array $criteria, array $orderBy = null)
 * @method Bla[]    findAll()
 * @method Bla[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BlaRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bla::class);
    }

    // /**
    //  * @return Bla[] Returns an array of Bla objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Bla
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
