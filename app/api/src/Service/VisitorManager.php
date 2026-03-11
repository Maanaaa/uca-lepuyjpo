<?php

namespace App\Service;

use App\Entity\Visiteur;
use App\Entity\Visite;
use App\Entity\PushSubscription;
use App\Repository\DepartementRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

class VisitorManager
{
    private $em;
    private $deptRepo;
    private $userRepo;
    private $hub;

    public function __construct(
        EntityManagerInterface $em,
        DepartementRepository $deptRepo,
        UtilisateurRepository $userRepo,
        HubInterface $hub
    ) {
        $this->em = $em;
        $this->deptRepo = $deptRepo;
        $this->userRepo = $userRepo;
        $this->hub = $hub;
    }

    public function createFullRegistration(array $data): Visite
    {
        $deptId = $data['departementId'] ?? $data['departement_id'] ?? null;
        $departement = $this->deptRepo->find($deptId);

        if (!$departement) {
            throw new \Exception("Département introuvable");
        }

        // 1. Création du Visiteur
        $visiteur = new Visiteur();
        $visiteur->setNom($data['nom'] ?? 'Anonyme');
        $visiteur->setPrenom($data['prenom'] ?? '');
        $visiteur->setEmail($data['email'] ?? '');
        $visiteur->setTelephone($data['telephone'] ?? '');
        $visiteur->setLycee($data['lycee'] ?? '');
        $visiteur->setVille($data['ville'] ?? '');
        $visiteur->setDepartement($departement);

        $this->em->persist($visiteur);

        // 2. Création de la Visite
        $visite = new Visite();
        $visite->setVisiteur($visiteur);
        $visite->setDepartement($departement);
        $visite->setStatut('ATTENTE');
        $visite->setDebut(new \DateTime());

        // --- LES LIGNES setVisiteurId(0) et setEtudiantId(0) ONT ÉTÉ SUPPRIMÉES ICI ---

        $this->em->persist($visite);
        $this->em->flush();

        // 3. Notification
        try {
            $this->notifyStudents($visite);
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire mais ne bloque pas le retour
        }

        return $visite;
    }

    public function notifyStudents(Visite $visite): void
    {
        $auth = [
            'VAPID' => [
                'subject' => $_ENV['VAPID_CONTACT'],
                'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
                'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
            ],
        ];

        $webPush = new WebPush($auth);
        $subscriptions = $this->em->getRepository(PushSubscription::class)->findAll();

        foreach ($subscriptions as $s) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $s->getEndpoint(),
                    'publicKey' => $s->getP256dh(),
                    'authToken' => $s->getAuth(),
                ]),
                json_encode([
                    'title' => 'Nouveau visiteur ! 📢',
                    'body' => ($visite->getVisiteur()->getPrenom() ?: 'Un visiteur') . ' attend un guide.',
                    'visiteId' => $visite->getId(),
                    'etudiantId' => $s->getUtilisateur()->getId()
                ])
            );
        }

        $webPush->flush();
    }

    public function acceptVisitor(int $visiteId, int $etudiantId): Visite
    {
        $visite = $this->em->getRepository(Visite::class)->find($visiteId);
        $etudiant = $this->userRepo->find($etudiantId);

        if (!$visite || !$etudiant) {
            throw new \Exception("Visite ou Étudiant non trouvé.");
        }

        if ($visite->getStatut() !== 'ATTENTE') {
            throw new \Exception("Cette visite n'est plus en attente.");
        }

        $visite->setStatut('EN_COURS');
        $visite->setEtudiant($etudiant);
        $etudiant->setIsDisponible(false);

        $this->em->flush();

        return $visite;
    }

    public function finishVisite(int $visiteId): Visite
    {
        $visite = $this->em->getRepository(Visite::class)->find($visiteId);

        if (!$visite) {
            throw new \Exception("Visite non trouvée.");
        }

        if ($visite->getStatut() !== 'EN_COURS') {
            throw new \Exception("Seule une visite en cours peut être terminée.");
        }

        $visite->setStatut('TERMINE');
        $visite->setFin(new \DateTime());

        $etudiant = $visite->getEtudiant();
        if ($etudiant) {
            $etudiant->setIsDisponible(true);
        }

        $this->em->flush();

        return $visite;
    }

    public function getWaitingList(int $departementId): array
    {
        $departement = $this->deptRepo->find($departementId);
        if (!$departement) {
            throw new \Exception("Département introuvable");
        }

        $visites = $this->em->getRepository(Visite::class)->findBy([
            'departement' => $departement,
            'statut' => 'ATTENTE'
        ], ['debut' => 'ASC']);

        return array_map(fn($v) => [
            'visiteId' => $v->getId(),
            'nom' => $v->getVisiteur()->getNom(),
            'prenom' => $v->getVisiteur()->getPrenom(),
            'heureArrivee' => $v->getDebut()->format('H:i')
        ], $visites);
    }
}