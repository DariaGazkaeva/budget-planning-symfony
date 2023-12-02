<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index() {
        return $this->render("index.html.twig");
    }

//    #[Route('/login', name: 'login', methods: 'GET')]
//    public function login() {
//        return $this->render("login.html.twig");
//    }
}