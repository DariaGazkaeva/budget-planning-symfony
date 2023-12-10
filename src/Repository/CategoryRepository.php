<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    private EntityManagerInterface $entityManager;
    public function __construct(ManagerRegistry $registry, EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, Category::class);
        $this->entityManager = $entityManager;
    }

    /**
     * @return Category[] Returns an array of Category objects
     */
    public function findAllByType($type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.is_income = :val')
            ->setParameter('val', $type)
            ->orderBy('c.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function save(Category $category) {
        $this->entityManager->persist($category);
        $this->entityManager->flush();
        return $category->getId();
    }

    public function update(Category $newCategory) {
        $category = $this->entityManager->find(Category::class, $newCategory->getId());
        $category->setName($newCategory->getName());
        $category->setIsIncome($newCategory->isIncome());
        $this->entityManager->flush();
    }

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
