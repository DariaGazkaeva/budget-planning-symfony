<?php

namespace App\Controller;

use App\Repository\MoneyOperationRepository;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request, MoneyOperationRepository $moneyOperationRepository) {
        $defaults = [
            'start' => new DateTime(date('Y-m-01')),
            'end' => new DateTime(date('Y-m-t'))
        ];
        $form = $this->createFormBuilder($defaults, ['method' => 'GET'])
            ->add('start', DateType::class, ['widget' => 'single_text'])
            ->add('end', DateType::class, ['widget' => 'single_text'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->render("history.html.twig", [
                'form' => $form->createView(),
                'history_income' => $moneyOperationRepository->findByOwnerAndTypeAndPeriod($this->userId, true, $form->getData()['start'], $form->getData()['end']),
                'history_expense' => $moneyOperationRepository->findByOwnerAndTypeAndPeriod($this->userId, false, $form->getData()['start'], $form->getData()['end'])
            ]);
        }

        return $this->render("history.html.twig", [
            'form' => $form->createView(),
            'history_income' => $moneyOperationRepository->findByOwnerAndTypeAndPeriod($this->userId, true, $defaults['start'], $defaults['end']),
            'history_expense' => $moneyOperationRepository->findByOwnerAndTypeAndPeriod($this->userId, false, $defaults['start'], $defaults['end'])
        ]);
    }
}