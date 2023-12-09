<?php

namespace App\Service;

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

        $balance = $this->security->getUser()->getBalance();
        if ($moneyOperation->isIncome()) {
            $balance += $moneyOperation->getSum();
        } else {
            $balance -= $moneyOperation->getSum();
        }
        $user = $this->security->getUser()->setBalance($balance);
        $this->userRepository->update($user);

        if (!$moneyOperation->isIncome()) {
            $limit = $this->limitService->findByCategory($moneyOperation->getCategory());
            if ($limit !== null) {
                $limit->setCurrentSum($limit->getCurrentSum() - $moneyOperation->getSum());
                $this->limitService->edit($limit);
            }
        }
    }
}