<?php

namespace App\Service;

use App\Entity\Category;
use App\Entity\MoneyOperation;
use App\Repository\MoneyOperationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\SecurityBundle\Security;

class MoneyOperationService
{
    private MoneyOperationRepository $moneyOperationRepository;
    private Security $security;
    private UserRepository $userRepository;
    private LimitService $limitService;

    public function __construct(MoneyOperationRepository $moneyOperationRepository, Security $security, UserRepository $userRepository, LimitService $limitService)
    {
        $this->moneyOperationRepository = $moneyOperationRepository;
        $this->security = $security;
        $this->userRepository = $userRepository;
        $this->limitService = $limitService;
    }

    public function getSumForMonth($userId, $type): float
    {
        $sum = 0;
        $start = date('Y-m-01');
        $end = date('Y-m-t');
        $operations = $this->moneyOperationRepository->findByOwnerAndTypeAndPeriod($userId, $type, $start, $end);
        foreach ($operations as $operation) {
            $sum += $operation->getSum();
        }
        return $sum;
    }

    public function add(MoneyOperation $moneyOperation): void
    {
        $this->moneyOperationRepository->save($moneyOperation);
        $this->changeBalance($moneyOperation->getSum(), $moneyOperation->isIncome());
        if (!$moneyOperation->isIncome()) {
            $this->changeLimit($moneyOperation->getSum(), $moneyOperation->getCategory());
        }
    }

    public function edit(MoneyOperation $moneyOperation, $oldSum): void
    {
        $this->moneyOperationRepository->update($moneyOperation);
        $x = -1 * ($oldSum - $moneyOperation->getSum());
        $this->changeBalance($x, $moneyOperation->isIncome());
        if (!$moneyOperation->isIncome()) {
            $this->changeLimit($x, $moneyOperation->getCategory());
        }
    }

    private function changeBalance($sum, bool $isIncome): void
    {
        $balance = $this->security->getUser()->getBalance();
        if ($isIncome) {
            $balance += $sum;
        } else {
            $balance -= $sum;
        }
        $user = $this->security->getUser()->setBalance($balance);
        $this->userRepository->update($user);
    }

    private function changeLimit($sum, Category $category): void
    {
        $limit = $this->limitService->findByCategory($category);
        if ($limit !== null) {
            $limit->setCurrentSum($limit->getCurrentSum() - $sum);
            $this->limitService->edit($limit);
        }
    }
}