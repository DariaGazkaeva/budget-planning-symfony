<?php

namespace App\Repository;

use App\Entity\MoneyOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MoneyOperation>
 *
 * @method MoneyOperation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MoneyOperation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MoneyOperation[]    findAll()
 * @method MoneyOperation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MoneyOperationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MoneyOperation::class);
    }

//    /**
//     * @return MoneyOperation[] Returns an array of MoneyOperation objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?MoneyOperation
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
