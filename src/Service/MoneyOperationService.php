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
            $this->changeLimit($moneyOperation->getSum(), $moneyOperation->getCategory(), $moneyOperation->getDate());
        }
    }

    public function edit(MoneyOperation $moneyOperation, MoneyOperation $oldMoneyOperation): void
    {
        $this->moneyOperationRepository->update($moneyOperation);
        $x = -1 * ($oldMoneyOperation->getSum() - $moneyOperation->getSum());
        $this->changeBalance($x, $moneyOperation->isIncome());
        if ($oldMoneyOperation->getCategory() === $moneyOperation->getCategory() && !$moneyOperation->isIncome()) {
            if ($moneyOperation->getDate() !== $oldMoneyOperation->getDate()) {
                $x = $moneyOperation->getSum();
            }
            $this->changeLimit($x, $moneyOperation->getCategory(), $moneyOperation->getDate());
        } else if ($oldMoneyOperation->getCategory() !== $moneyOperation->getCategory() && !$moneyOperation->isIncome()) {
            $this->changeLimit($oldMoneyOperation->getSum() * -1, $oldMoneyOperation->getCategory(), $oldMoneyOperation->getDate());
            $this->changeLimit($moneyOperation->getSum(), $moneyOperation->getCategory(), $moneyOperation->getDate());
        }
    }

    public function delete(MoneyOperation $moneyOperation): void
    {
        $this->moneyOperationRepository->delete($moneyOperation);
        $this->changeBalance($moneyOperation->getSum() * -1, $moneyOperation->isIncome());
        if (!$moneyOperation->isIncome()) {
            $this->changeLimit($moneyOperation->getSum() * -1, $moneyOperation->getCategory(), $moneyOperation->getDate());
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

    private function changeLimit($sum, Category $category, $date): void
    {
        $limit = $this->limitService->findByCategoryAndDate($category, $date);
        if ($limit !== null) {
            $max = $limit->getTotalSum();
            $current = $limit->getCurrentSum() - $sum;
            if ($current > $max) {
                $current = $max;
            }
            $limit->setCurrentSum($current);
            $this->limitService->edit($limit, $limit);
        }
    }

    public function findByOwnerAndTypeAndPeriod(int $userId, bool $type, mixed $start, mixed $end)
    {
        return $this->moneyOperationRepository->findByOwnerAndTypeAndPeriod($userId, $type, $start, $end);
    }
}