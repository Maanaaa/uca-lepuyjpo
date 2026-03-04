<?php

namespace App\Controller;

use App\Service\UserManager; 
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class CreateStudentController extends AbstractController
{
    #[Route('/api/create-student', name: 'app_admin_create_student', methods: ['POST'])]
    public function create(Request $request, UserManager $userManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['prenom'], $data['departementId'])) {
            return $this->json(['error' => 'Données incomplètes'], 400);
        }

        try {
            $student = $userManager->createStudent($data);

            return $this->json([
                'message' => 'Étudiant créé',
                'identifiant' => $student->getEmail(),
                'departement' => $student->getDepartement()->getNom()
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}