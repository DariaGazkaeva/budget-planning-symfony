<?php

namespace App\Repository;

use App\Entity\MoneyOperation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, MoneyOperation::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @return MoneyOperation[] Returns an array of MoneyOperation objects
     */
    public function findByOwnerAndTypeAndPeriod($userId, $type, $start, $end): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.owner = :user_id')
            ->andWhere('m.is_income = :type')
            ->andWhere('m.date BETWEEN :start AND :end')
            ->setParameter('user_id', $userId)
            ->setParameter('type', $type)
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('m.date', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(MoneyOperation $moneyOperation) {
        $this->entityManager->persist($moneyOperation);
        $this->entityManager->flush();
    }

    public function delete(MoneyOperation $moneyOperation) {

    }

    public function update(MoneyOperation $moneyOperation) {
        $operation = $this->entityManager->find(MoneyOperation::class, $moneyOperation->getId());
        $operation->setDescription($moneyOperation->getDescription());
        $operation->setSum($moneyOperation->getSum());
        $operation->setDate($moneyOperation->getDate());
        $operation->setCategory($moneyOperation->getCategory());
        $this->entityManager->flush();
    }

//    public function findById($id): ?MoneyOperation
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.id = :id')
//            ->setParameter('id', $id)
//            ->getQuery()
//            ->getOneOrNullResult();
//    }
}
