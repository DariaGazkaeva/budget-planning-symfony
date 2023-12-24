<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Limit;
use App\Entity\MoneyOperation;
use App\Repository\UserRepository;
use App\Service\CategoryService;
use App\Service\MoneyOperationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\LimitService;

class ProfileController extends AbstractController
{
    private LimitService $limitService;
    private Security $security;
    private int $userId;
    private MoneyOperationService $moneyOperationService;
    private CategoryService $categoryService;

    public function __construct(Security $security, MoneyOperationService $moneyOperationService, CategoryService $categoryService, LimitService $limitService)
    {
        $this->security = $security;
        $this->userId = $this->security->getUser()->getId();
        $this->moneyOperationService = $moneyOperationService;
        $this->categoryService = $categoryService;
        $this->limitService = $limitService;
    }
    #[Route('/profile', name: 'profile')]
    public function index(Request $request) {
        $incomeSum = $this->moneyOperationService->getSumForMonth($this->userId, true);
        $expenseSum = $this->moneyOperationService->getSumForMonth($this->userId, false);

        $limits = $this->limitService->findAllByUserId($this->userId);

        $incomeForm = $this->createMoneyOperationForm(true);
        $expenseForm = $this->createMoneyOperationForm(false);
        $incomeForm->handleRequest($request);
        $expenseForm->handleRequest($request);

        if ($incomeForm->isSubmitted() && $incomeForm->isValid()) {
            $data = $incomeForm->getData();
            $moneyOperation = new MoneyOperation();
            $moneyOperation->setIsIncome(true);
            $moneyOperation->setCategory($data['category']);
            $moneyOperation->setSum($data['sum']);
            $moneyOperation->setDate($data['date']);
            $moneyOperation->setDescription($data['description']);
            $moneyOperation->setOwner($this->security->getUser());
            $this->moneyOperationService->add($moneyOperation);
            return $this->redirectToRoute("profile");
        }

        if ($expenseForm->isSubmitted() && $expenseForm->isValid()) {
            $data = $expenseForm->getData();
            $moneyOperation = new MoneyOperation();
            $moneyOperation->setIsIncome(false);
            $moneyOperation->setCategory($data['category']);
            $moneyOperation->setSum($data['sum']);
            $moneyOperation->setDate($data['date']);
            $moneyOperation->setDescription($data['description']);
            $moneyOperation->setOwner($this->security->getUser());
            $this->moneyOperationService->add($moneyOperation);
            return $this->redirectToRoute("profile");
        }
        return $this->render("profile.html.twig",
            [
                'user' => $this->security->getUser(),
                'income_sum' => $incomeSum,
                'expense_sum' => $expenseSum,
                'income_form' =>$incomeForm->createView(),
                'expense_form' =>$expenseForm->createView(),
                'limits' => $limits,
                'income_categories' => $this->categoryService->findAllByTypeAndUserId(true, $this->userId),
                'expense_categories' => $this->categoryService->findAllByTypeAndUserId(false, $this->userId)
            ]);
    }

    private function createMoneyOperationForm(bool $type) : FormInterface {
        return $this->createFormBuilder()
            ->add('category', ChoiceType::class, [
                'choices' => $this->categoryService->findAllByTypeAndUserId($type, $this->userId),
                'choice_value' => 'id',
                'choice_label' => function (Category $category): string {
                    return $category->getName();
                },
            ])
            ->add('sum', NumberType::class)
            ->add('date', DateType::class, ['widget' => 'single_text',])
            ->add('description', TextType::class)
            ->getForm();
    }

    #[Route('/profile/balance', name: 'change_balance')]
    public function changeBalance(Request $request, UserRepository $userRepository) {
        $defaults = [
            'balance' => $this->security->getUser()->getBalance(),
        ];
        $form = $this->createFormBuilder($defaults)
            ->add('balance', NumberType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $balance = $form->getData()['balance'];
            $user = $this->security->getUser()->setBalance($balance);
            $userRepository->update($user);
            return $this->redirectToRoute("profile");
        }

        return $this->render("balance.html.twig", [
            'form' => $form->createView()
        ]);
    }

    #[Route('/profile/limit/{id}', name: 'change_limit')]
    public function changeLimit(Limit $limit, Request $request) {
        $defaults = [
            'category' => $limit->getCategory(),
            'total_sum' => $limit->getTotalSum(),
            'current_sum' => $limit->getCurrentSum()
        ];
        $form = $this->createFormBuilder($defaults)
            ->add('category', ChoiceType::class, [
                'choices' => $this->categoryService->findAllByTypeAndUserId(false, $this->userId),
                'choice_value' => 'id',
                'choice_label' => function (Category $category): string {
                    return $category->getName();
                },
            ])
            ->add('total_sum', NumberType::class)
            ->add('current_sum', NumberType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $limit;
            $updated->setCategory($form->getData()['category']);
            $updated->setCurrentSum($form->getData()['current_sum']);
            $updated->setTotalSum($form->getData()['total_sum']);
            $this->limitService->edit($updated);
            return $this->redirectToRoute('profile');
        }
        return $this->render("operation.html.twig", ['form' => $form]);
    }

    #[Route('/profile/limit', name: 'create_limit')]
    public function createLimit(Request $request) {
        $form = $this->createFormBuilder()
            ->add('category', ChoiceType::class, [
                'choices' => $this->categoryService->findAllByTypeAndUserId(false, $this->userId),
                'choice_value' => 'id',
                'choice_label' => function (Category $category): string {
                    return $category->getName();
                },
            ])
            ->add('total_sum', NumberType::class)
            ->add('current_sum', NumberType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $limit = new Limit();
            $limit->setCategory($form->getData()['category']);
            $limit->setCurrentSum($form->getData()['current_sum']);
            $limit->setTotalSum($form->getData()['total_sum']);
            $limit->setOwner($this->security->getUser());
            $this->limitService->add($limit);
            return $this->redirectToRoute('profile');
        }
        return $this->render("operation.html.twig", ['form' => $form]);
    }

    #[Route('/profile/category', name: 'create_category', methods: 'POST')]
    public function createCategory() {
        $category = new Category();
        $category->setOwner($this->security->getUser());
        $category->setIsIncome($_POST['income'] === 'true');
        $category->setName($_POST['name']);
        $id = $this->categoryService->save($category);
        return new JsonResponse([
            'id' => $id,
            'name' => $category->getName()
        ], 200);
    }

    #[Route('/profile/category/{id}', name: 'change_category')]
    public function changeCategory(Category $category, Request $request) {
        $defaults = [
            'name' => $category->getName(),
            'is_income' => $category->isIncome()
        ];
        $form = $this->createFormBuilder($defaults)
            ->add('is_income', ChoiceType::class, [
                'choices' => ['Income' => true, 'Expense' => false],
            ])
            ->add('name', TextType::class)
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $updated = $category;
            $updated->setName($form->getData()['name']);
            $updated->setIsIncome($form->getData()['is_income'] === '1');
            $this->categoryService->update($updated);
            return $this->redirectToRoute('profile');
        }
        return $this->render("operation.html.twig", ['form' => $form]);
    }

    #[Route('/profile/delete-category/{id}', name: 'delete_category', methods: 'DELETE')]
    public function deleteCategory(Category $category) {
        if ($category->getOwner()->getId() === $this->userId) {
            $this->categoryService->delete($category);
            return new JsonResponse(['categoryId' => $category->getId()], 200);
        } else {
            return new JsonResponse(status: 400);
        }
    }

    #[Route('/profile/delete-limit/{id}', name: 'delete_limit', methods: 'DELETE')]
    public function deleteLimit(Limit $limit) {
        if ($limit->getOwner()->getId() === $this->userId) {
            $this->limitService->delete($limit);
            return new JsonResponse(status: 200);
        } else {
            return new JsonResponse(status: 400);
        }
    }
}