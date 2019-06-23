<?php

namespace App\Repository;

use App\Entity\Pony;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Pony|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pony|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pony[]    findAll()
 * @method Pony[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PonyRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Pony::class);
    }

    // /**
    //  * @return Pony[] Returns an array of Pony objects
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
    public function findOneBySomeField($value): ?Pony
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function add(Pony $pony):void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->persist($pony);
        $entityManager->flush();
    }

    public function remove(Pony $pony):void
    {
        $entityManager = $this->getEntityManager();
        $entityManager->remove($pony);
        $entityManager->flush();
    }
}
