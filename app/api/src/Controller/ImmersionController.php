<?php

namespace App\Controller;

use App\Entity\Visiteur;
use App\Entity\JourneeImmersion;
use App\Entity\InscriptionImmersion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class ImmersionController extends AbstractController
{
    #[Route('/api/inscription-immersion', name: 'api_immersion_register', methods: ['POST'])]
    public function registerImmersion(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $visitorId = $data['vId'] ?? null;
        $journeeId = $data['journeeId'] ?? null;
        $deptSlug = $data['dept'] ?? null; 

        if (!$visitorId) {
            return new JsonResponse(['error' => 'ID visiteur manquant'], 400);
        }

        if (!$journeeId) {
            return new JsonResponse(['error' => 'ID journée manquant'], 400);
        }

        $visiteur = $em->getRepository(Visiteur::class)->find($visitorId);

        if (!$visiteur) {
            return new JsonResponse(['error' => 'Visiteur non trouvé'], 404);
        }

        $journee = $em->getRepository(JourneeImmersion::class)->find($journeeId);

        if (!$journee) {
            return new JsonResponse(['error' => 'Journée d\'immersion non trouvée'], 404);
        }

        $inscription = new InscriptionImmersion();
        $inscription->setVisiteur($visiteur);
        $inscription->setJourneeImmersion($journee);
        
        $em->persist($inscription);
        $em->flush();

        $departement = $visiteur->getDepartement();

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Inscription validée !',
            'data' => [
                'nom' => $visiteur->getNom(),
                'prenom' => $visiteur->getPrenom(),
                'departement' => $departement->getNom(),
                'date_immersion' => $journee->getDate()->format('d/m/Y'),
                'email' => $visiteur->getEmail(),
                'telephone' => $visiteur->getTelephone(),
                'lycee' => $visiteur->getLycee(),
                'ville' => $visiteur->getVille(),
                'pays' => $visiteur->getPays(),
                'etudes' => $visiteur->getEtudes()
            ]
        ], 200);
    }
}
