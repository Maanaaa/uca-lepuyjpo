<?php

namespace App\Controller;

use App\Entity\Visiteur;
use App\Service\VisitorManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class VisitorRegistrationController extends AbstractController
{
    #[Route('/api/register-visitor', name: 'app_visitor_registration', methods: ['POST'])]
    public function register(Request $request, VisitorManager $visitorManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // On récupère le tableau (visite + qrCode)
        $result = $visitorManager->createFullRegistration($data);
        $visite = $result['visite'];
        $qrCode = $result['qrCode'];

        return $this->json([
            'message' => 'Visiteur inscrit et visite créée',
            'visiteId' => $visite->getId(),
            'qrCode' => $qrCode // Ton Next.js recevra le base64 ici !
        ]);
    }
}