<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\Limit;
use App\Repository\LimitRepository;
use App\Repository\MoneyOperationRepository;
use Symfony\Bundle\SecurityBundle\Security;

class LimitService
{
    private LimitRepository $limitRepository;
    private Security $security;
    private MoneyOperationRepository $moneyOperationRepository;

    public function __construct(LimitRepository $limitRepository, Security $security, MoneyOperationRepository $moneyOperationRepository)
    {
        $this->limitRepository = $limitRepository;
        $this->security = $security;
        $this->moneyOperationRepository = $moneyOperationRepository;
    }
    public function add(Limit $limit)
    {
        return $this->limitRepository->save($limit);
    }

    public function findAllByUserId($userId): array
    {
        return $this->limitRepository->findAllByOwner($userId);
    }

    public function edit(Limit $limit, Limit $oldLimit) {
        if ($limit->getStartDate() != $oldLimit->getStartDate()) {
            $sum = 0;
            $operations = $this->moneyOperationRepository->findAllByCategoryAndDate(
                $this->security->getUser()->getId(),
                $limit->getCategory(),
                $limit->getStartDate()
            );
            foreach ($operations as $operation) {
                $sum += $operation->getSum();
            }
            $limit->setCurrentSum($limit->getTotalSum() - $sum);
        }
        $this->limitRepository->update($limit);
    }

    public function delete(Limit $limit) {
        $this->limitRepository->delete($limit);
    }

    public function findByCategoryAndDate(Category $category, $date) {
        return $this->limitRepository->findByCategoryAndOwnerIdAndDate($category, $this->security->getUser()->getId(), $date);
    }

    public function findByCategoryAndOwner(Category $category, $owner)
    {
        return $this->limitRepository->findByCategoryAndOwner($category, $owner);
    }
}