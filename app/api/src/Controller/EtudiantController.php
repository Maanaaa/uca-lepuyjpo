<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class EtudiantController extends AbstractController
{
    #[Route('/student/dashboard', name: 'app_student_dashboard')]
    public function index(): Response
    {
        return $this->render('etudiant/index.html.twig');
    }

    #[Route('/student/toggle-status', name: 'app_student_toggle_status', methods: ['POST'])]
    public function toggleStatus(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        if ($user) {
            $user->setIsDisponible(!$user->getIsDisponible());
            $em->flush();
        }

        return $this->redirectToRoute('app_student_dashboard');
    }
}