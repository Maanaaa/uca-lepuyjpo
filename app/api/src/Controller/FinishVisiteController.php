<?php
namespace App\Controller;

use App\Service\VisitorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class FinishVisiteController extends AbstractController
{
    #[Route('/api/visite/finish', name: 'app_visite_finish', methods: ['POST'])]
    public function finish(Request $request, VisitorManager $visitorManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['visiteId'])) {
            return $this->json(['error' => 'ID de visite manquant'], 400);
        }

        try {
            $visite = $visitorManager->finishVisite((int) $data['visiteId']);

            return $this->json([
                'message' => 'Visite terminée, étudiant de nouveau disponible',
                'visiteId' => $visite->getId(),
                'statut' => $visite->getStatut(),
                'duree' => $visite->getDebut()->diff($visite->getFin())->format('%i minutes')
            ]);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}