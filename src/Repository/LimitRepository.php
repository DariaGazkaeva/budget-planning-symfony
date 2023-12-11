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

    public function findByCategoryAndOwnerId(Category $category, $owner): ?Limit
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
