<?php

namespace App\Service;

use App\Entity\Category;
use App\Repository\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
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
        $this->categoryRepository->delete($category);
    }
}