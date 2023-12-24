<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\MoneyOperation;
use App\Repository\CategoryRepository;
use App\Repository\MoneyOperationRepository;
use App\Service\MoneyOperationService;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\JsonResponse;
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

    #[Route('/profile/money-operation/{id}', name: 'edit_money_operation')]
    public function editGET(MoneyOperation $moneyOperation, MoneyOperationService $moneyOperationService, Request $request, CategoryRepository $categoryRepository) {
        $defaults = [
            'category' => $moneyOperation->getCategory(),
            'sum' => $moneyOperation->getSum(),
            'date' => $moneyOperation->getDate(),
            'description' => $moneyOperation->getDescription()
        ];
        $form = $this->createFormBuilder($defaults)
            ->add('category', ChoiceType::class, [
                'choices' => $categoryRepository->findAllByTypeAndUserId($moneyOperation->isIncome(), $this->userId),
                'choice_value' => 'id',
                'choice_label' => function (Category $category): string {
                    return $category->getName();
                },
            ])
            ->add('sum', NumberType::class)
            ->add('date', DateType::class, ['widget' => 'single_text',])
            ->add('description', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $oldSum = $moneyOperation->getSum();
            $updated = $moneyOperation;
            $updated->setDate($form->getData()['date']);
            $updated->setSum($form->getData()['sum']);
            $updated->setCategory($form->getData()['category']);
            $updated->setDescription($form->getData()['description']);
            $moneyOperationService->edit($updated, $oldSum);
            return $this->redirectToRoute('history');
        }
        return $this->render("operation.html.twig", ['form' => $form]);
    }

    #[Route('/profile/delete-money-operation/{id}', name: 'delete_money_operation', methods: 'DELETE')]
    public function delete(MoneyOperation $moneyOperation, MoneyOperationService $moneyOperationService) {
        $moneyOperationService->delete($moneyOperation);
        return new JsonResponse(status: 200);
    }
}