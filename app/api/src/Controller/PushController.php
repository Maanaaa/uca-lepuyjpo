<?php

namespace App\Controller;

use App\Entity\PushSubscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PushController extends AbstractController
{
    #[Route('/api/push-subscribe', name: 'push_subscribe', methods: ['POST'])]
    public function subscribe(Request $request, EntityManagerInterface $em)
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->getUser();

        if (!$user) return $this->json(['error' => 'Non connecté'], 403);

        $sub = new PushSubscription();
        $sub->setEndpoint($data['endpoint']);
        $sub->setP256dh($data['keys']['p256dh']);
        $sub->setAuth($data['keys']['auth']);
        $sub->setUtilisateur($user);

        $em->persist($sub);
        $em->flush();

        return $this->json(['success' => true]);
    }
}