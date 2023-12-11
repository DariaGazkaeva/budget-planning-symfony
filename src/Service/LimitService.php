<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Limit;
use App\Repository\LimitRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class LimitService
{
    private LimitRepository $limitRepository;
    private Security $security;
    private UserRepository $userRepository;

    public function __construct(LimitRepository $limitRepository, Security $security, UserRepository $userRepository)
    {
        $this->limitRepository = $limitRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
    }
    public function add(Limit $limit): void
    {
        $this->limitRepository->save($limit);
    }

    public function findAllByUserId($userId): array
    {
        return $this->limitRepository->findAllByOwner($userId);
    }

    public function edit(Limit $limit) {
        $this->limitRepository->update($limit);
    }

    public function delete(Limit $limit) {
        $this->limitRepository->delete($limit);
    }

    public function findByCategory(Category $category) {
        return $this->limitRepository->findByCategoryAndOwnerId($category, $this->security->getUser()->getId());
    }
}