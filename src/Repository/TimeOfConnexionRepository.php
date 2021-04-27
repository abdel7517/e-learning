<?php

namespace App\Repository;

use App\Entity\TimeOfConnexion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method TimeOfConnexion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TimeOfConnexion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TimeOfConnexion[]    findAll()
 * @method TimeOfConnexion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TimeOfConnexionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeOfConnexion::class);
    }

    // /**
    //  * @return TimeOfConnexion[] Returns an array of TimeOfConnexion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    public function findByDate($date)
    {
        $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
        $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

        $qb = $this->createQueryBuilder("e");
        $qb
            ->andWhere('e.toDay BETWEEN :from AND :to')
            ->setParameter('from', $from )
            ->setParameter('to', $to)
        ;
        $result = $qb->getQuery()->getResult();

        return $result;
    }
}
