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

    /**
     * @return MoneyOperation[] Returns an array of MoneyOperation objects
     */
    public function findByOwnerAndType($userId, $type): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.owner = :user_id')
            ->andWhere('m.is_income = :type')
            ->setParameter('user_id', $userId)
            ->setParameter('type', $type)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

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
