<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }
    #[Route('/profile', name: 'profile')]
    public function index() {
        return $this->render("profile.html.twig",
            ['user' => $this->security->getUser(), 'balance_modal' => false]);
    }

    #[Route('/profile', name: 'profile_init')]
    public function index_init() {
        return $this->render("profile.html.twig",
            ['user' => $this->security->getUser(), 'balance_modal' => true]);
    }

    #[Route('/profile/balance', name: 'change_balance')]
    public function change_balance(Request $request, UserRepository $userRepository) {
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
}