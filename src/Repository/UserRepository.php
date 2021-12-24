<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function getByDateAndMarket(\Datetime $date, int $market_id)
{
    $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
    $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

    $qb = $this->createQueryBuilder("e");
    $qb
        ->andWhere('e.end BETWEEN :from AND :to')
        ->andWhere('e.market_id LIKE :market_id')
        ->setParameter('from', $from )
        ->setParameter('to', $to)
        ->setParameter('market_id', $market_id);
    $result = $qb->getQuery()->getResult();

    return $result;
}

public function getByDateStartAndMarket(\Datetime $date, int $market_id)
{
    $from = new \DateTime($date->format("Y-m-d")." 00:00:00");
    $to   = new \DateTime($date->format("Y-m-d")." 23:59:59");

    $qb = $this->createQueryBuilder("e");
    $qb->andWhere('e.start BETWEEN :from AND :to')
    ->andWhere('e.market_id LIKE :market_id')
        ->setParameter('from', $from )
        ->setParameter('to', $to)
        ->setParameter('market_id', $market_id);

    $result = $qb->getQuery()->getResult();

    return $result;
}
}
