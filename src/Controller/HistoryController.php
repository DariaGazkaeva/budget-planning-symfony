<?php

namespace App\Controller;

use App\Repository\MoneyOperationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    private Security $security;
    private int $userId;
    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->userId = $this->security->getUser()->getId();
    }
    #[Route('/profile/history', name: 'history')]
    public function index(MoneyOperationRepository $moneyOperationRepository) {
        return $this->render("history.html.twig", [
            'history_income' => $moneyOperationRepository->findByOwnerAndType($this->userId, true),
            'history_expense' => $moneyOperationRepository->findByOwnerAndType($this->userId, false)
        ]);
    }
}