<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    // Laisse cette route, même vide, ça évite que Symfony panique
    #[Route(path: '/login', name: 'app_login')]
    public function login(): Response
    {

        return $this->redirect('http://localhost:3000/connexion-admin');
    }

    #[Route(path: '/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {

    }
}