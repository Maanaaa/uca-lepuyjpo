<?php
namespace App\Controller;

use App\Service\VisitorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class AcceptVisitorController extends AbstractController
{
    #[Route('/api/visite/accept', name: 'app_visite_accept', methods: ['POST'])]
    public function accept(Request $request, VisitorManager $visitorManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['visiteId']) || !isset($data['etudiantId'])) {
            return $this->json(['error' => 'IDs manquants'], 400);
        }

        try {
            $visite = $visitorManager->acceptVisitor($data['visiteId'], $data['etudiantId']);

            return $this->json([
                'message' => 'Visite acceptée',
                'visiteId' => $visite->getId(),
                'newStatus' => $visite->getStatut(),
                'etudiant' => $visite->getEtudiant()->getNom()
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}