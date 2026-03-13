<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminAuthController extends AbstractController
{
    #[Route('/api/admin-auth/login', name: 'api_admin_login', methods: ['POST'])]
    public function login(
        Request $request, 
        UserPasswordHasherInterface $hasher, 
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage
    ): JsonResponse {
        try {
            $data = json_decode($request->getContent(), true);
            $email = $data['email'] ?? null;
            $password = $data['mdp'] ?? null;

            if (!$email || !$password) {
                return new JsonResponse(['error' => 'Données manquantes'], 400);
            }

            $user = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);

            if (!$user || !$hasher->isPasswordValid($user, $password)) {
                return new JsonResponse(['error' => 'Identifiants invalides'], 401);
            }

            $session = $request->getSession();
            if (!$session->isStarted()) {
                $session->start();
            }

            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $tokenStorage->setToken($token);
            $session->set('_security_main', serialize($token));

            return new JsonResponse(['success' => true]);

        } catch (\Throwable $e) {
            return new JsonResponse([
                'error' => 'Crash PHP',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}