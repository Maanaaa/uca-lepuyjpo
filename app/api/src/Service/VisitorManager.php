<?php

namespace App\Service;

use App\Entity\Visiteur;
use App\Entity\Visite;
use App\Entity\PushSubscription;
use App\Repository\DepartementRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;
use App\Service\QrCodeGenerator; 

class VisitorManager
{
    private $em;
    private $deptRepo;
    private $userRepo;
    private $hub;
    private $qrCodeGenerator; 

    public function __construct(
        EntityManagerInterface $em,
        DepartementRepository $deptRepo,
        UtilisateurRepository $userRepo,
        HubInterface $hub,
        QrCodeGenerator $qrCodeGenerator 
    ) {
        $this->em = $em;
        $this->deptRepo = $deptRepo;
        $this->userRepo = $userRepo;
        $this->hub = $hub;
        $this->qrCodeGenerator = $qrCodeGenerator; 
    }

    /**
     * Crée le visiteur, la visite, envoie le Push, publie sur Mercure et génère le QR Code.
     */
    public function createFullRegistration(array $data): array 
    {
        $deptId = $data['departementId'] ?? $data['departement_id'] ?? null;
        $departement = $this->deptRepo->find($deptId);

        if (!$departement) {
            throw new \Exception("Département introuvable");
        }

        $visiteur = new Visiteur();
        $visiteur->setNom($data['nom'] ?? 'Anonyme');
        $visiteur->setPrenom($data['prenom'] ?? '');
        $visiteur->setEmail($data['email'] ?? '');
        $visiteur->setTelephone($data['telephone'] ?? '');
        $visiteur->setLycee($data['lycee'] ?? '');
        $visiteur->setVille($data['ville'] ?? '');
        $visiteur->setPays($data['Pays'] ?? null);                    
        $visiteur->setDepartementOrigine($data['departementOrigine'] ?? null);
        $visiteur->setEtudes($data['etudes'] ?? null);
        $visiteur->setDepartement($departement);
        $this->em->persist($visiteur);
        $visite = new Visite();
        $visite->setVisiteur($visiteur);
        $visite->setDepartement($departement);
        $visite->setStatut('ATTENTE');
        $visite->setDebut(new \DateTime());
        $this->em->persist($visite);

        $this->em->flush();

        // Notif Push
        $this->notifyStudents($visite);

        // Mercure pour mise à jour dashboard
        try {
            $update = new Update(
                'https://jpo.uca.fr/visites', // Faux lien juste pour que Mercure fonctionne
                json_encode([
                    'type' => 'NEW_VISITOR',
                    'prenom' => $visiteur->getPrenom(),
                    'dept' => $departement->getNom(),
                    'heure' => $visite->getDebut()->format('H:i')
                ])
            );
            $this->hub->publish($update);
        } catch (\Exception $e) {
            
        }

        // Génération qr
        $qrCodeUri = $this->qrCodeGenerator->generateUri([
            'id' => $visiteur->getId(),
            'nom' => $visiteur->getNom(),
            'prenom' => $visiteur->getPrenom(),
            'email' => $visiteur->getEmail(),
        ]);

        return [
            'visite' => $visite,
            'qrCode' => $qrCodeUri
        ];
    }

    // Notif push via VAPID
    public function notifyStudents(Visite $visite): void
    {
        // On ignore les notices GMP/BCMath pour le mode dev
        $oldLevel = error_reporting(E_ALL & ~E_USER_NOTICE);

        try {
            $auth = [
                'VAPID' => [
                    'subject' => 'mailto:' . $_ENV['VAPID_CONTACT'],
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
                        'etudiantId' => $s->getUtilisateur() ? $s->getUtilisateur()->getId() : null
                    ])
                );
            }

            foreach ($webPush->flush() as $report) {
                if (!$report->isSuccess() && $report->isSubscriptionExpired()) {
                    $sub = $this->em->getRepository(PushSubscription::class)->findOneBy(['endpoint' => $report->getEndpoint()]);
                    if ($sub) { $this->em->remove($sub); }
                }
            }
            $this->em->flush();

        } catch (\Exception $e) {
            // Log silencieux
        } finally {
            error_reporting($oldLevel);
        }
    }

    
     // Assigne un étudiant à une visite.
     
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

    // Termine une visite et libère l'étudiant.
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

     // Récupère la file d'attente d'un département.
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