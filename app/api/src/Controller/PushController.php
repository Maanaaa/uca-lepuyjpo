<?php

namespace App\Controller;

use App\Entity\PushSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse; 
use Symfony\Component\Routing\Attribute\Route;

class PushController extends AbstractController
{
    #[Route('/api/push-subscribe', name: 'api_push_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser(); 

        if (!$data || !isset($data['endpoint'])) {
            return $this->json(['error' => 'Données invalides'], 400);
        }

        $sub = $em->getRepository(PushSubscription::class)->findOneBy(['endpoint' => $data['endpoint']]) 
            ?? new PushSubscription();
        
        $sub->setEndpoint($data['endpoint']);

        $sub->setP256dh($data['keys']['p256dh'] ?? null);
        $sub->setAuth($data['keys']['auth'] ?? null);
        $sub->setUtilisateur($user);

        $em->persist($sub);
        $em->flush();

        return $this->json(['success' => true]);
    }
}