<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Limit;
use App\Repository\CategoryRepository;
use App\Repository\LimitRepository;
use App\Repository\MoneyOperationRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    private MoneyOperationRepository $moneyOperationRepository;
    private LimitRepository $limitRepository;
    public function __construct(LimitRepository $limitRepository, CategoryRepository $categoryRepository, MoneyOperationRepository $moneyOperationRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->moneyOperationRepository = $moneyOperationRepository;
        $this->limitRepository = $limitRepository;
    }

    public function findAllByTypeAndUserId(bool $type, int $userId)
    {
        return $this->categoryRepository->findAllByTypeAndUserId($type, $userId);
    }

    public function save(Category $category)
    {
        return $this->categoryRepository->save($category);
    }

    public function update(Category $updated)
    {
        $this->categoryRepository->update($updated);
    }

    public function delete(Category $category)
    {
        $operations = $this->moneyOperationRepository->findAllByCategory($category);
        $limits = $this->limitRepository->findAllByCategory($category);
        if (count($operations) === 0 && count($limits) === 0) {
            $this->categoryRepository->delete($category);
            return true;
        } else {
            return false;
        }
    }
}