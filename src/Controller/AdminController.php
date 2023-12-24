<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin')]
    public function index(UserService $userService) {
        return $this->render("admin.html.twig", ['users' => $userService->findAll(), 'form' => null]);
    }

    #[Route('/admin/change/{id}', name: 'change_user')]
    public function changeUser(User $user, UserService $userService, Request $request) {
        $defaults = [
            'balance' => $user->getBalance(),
            'name' => $user->getName(),
            'email' => $user->getEmail()
        ];
        $form = $this->createFormBuilder($defaults)
            ->add('name', TextType::class)
            ->add('email', TextType::class)
            ->add('balance', TextType::class)
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setName($form->getData()['name']);
            $user->setEmail($form->getData()['email']);
            $user->setBalance($form->getData()['balance']);
            $userService->update($user);
            return $this->redirectToRoute('admin');
        }
        return $this->render("admin.html.twig", ['users' => $userService->findAll(), 'form' => $form]);
    }
}