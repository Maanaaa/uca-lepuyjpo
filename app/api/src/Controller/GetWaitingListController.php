<?php

namespace App\Controller;

use App\Service\VisitorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class GetWaitingListController extends AbstractController
{
    #[Route('/api/visites/waiting/{departementId}', name: 'app_visites_waiting', methods: ['GET'])]
    public function list(int $departementId, VisitorManager $visitorManager): JsonResponse
    {
        try {
            return $this->json($visitorManager->getWaitingList($departementId));
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 500);
        }
    }
}