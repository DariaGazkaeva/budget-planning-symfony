<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Limit;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Limit>
 *
 * @method Limit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Limit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Limit[]    findAll()
 * @method Limit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LimitRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Limit::class);
        $this->entityManager = $entityManager;
    }

    public function save(Limit $limit) {
        $this->entityManager->persist($limit);
        $this->entityManager->flush();
    }

    public function update(Limit $newLimit) {
        $limit = $this->entityManager->find(Limit::class, $newLimit->getId());
        $limit->setCurrentSum($newLimit->getCurrentSum());
        $limit->setTotalSum($newLimit->getTotalSum());
        $limit->setCategory($newLimit->getCategory());
        $limit->setStartDate($newLimit->getStartDate());
        $this->entityManager->flush();
    }

    public function delete(Limit $limit) {
        $this->entityManager->remove($limit);
        $this->entityManager->flush();
    }

    /**
     * @return Limit[] Returns an array of Limit objects
     */
    public function findAllByOwner($userId): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.owner = :val')
            ->setParameter('val', $userId)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Limit[] Returns an array of Limit objects
     */
    public function findAllByCategory(Category $category): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.category = :val')
            ->setParameter('val', $category)
            ->orderBy('l.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByCategoryAndOwnerIdAndDate(Category $category, $owner, $date): ?Limit
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.category = :c')
            ->andWhere('l.owner = :o')
            ->andWhere('l.start_date <= :d')
            ->setParameter('c', $category->getId())
            ->setParameter('o', $owner)
            ->setParameter('d', $date)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByCategoryAndOwner(Category $category, $owner)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.category = :c')
            ->andWhere('l.owner = :o')
            ->setParameter('c', $category->getId())
            ->setParameter('o', $owner)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
