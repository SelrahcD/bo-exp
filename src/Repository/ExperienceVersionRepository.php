<?php

namespace App\Repository;

use App\Entity\ExperienceVersion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method ExperienceVersion|null find($id, $lockMode = null, $lockVersion = null)
 * @method ExperienceVersion|null findOneBy(array $criteria, array $orderBy = null)
 * @method ExperienceVersion[]    findAll()
 * @method ExperienceVersion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExperienceVersionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, ExperienceVersion::class);
    }

    // /**
    //  * @return ExperienceVersion[] Returns an array of ExperienceVersion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ExperienceVersion
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
