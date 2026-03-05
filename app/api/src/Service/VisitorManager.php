<?php

namespace App\Service;

use App\Entity\Visiteur;
use App\Entity\Visite;
use App\Entity\Departement;
use App\Repository\DepartementRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class VisitorManager
{
    private $em;

    private $deptRepo;
    private $userRepo; // Ajouté
    private $hub;      // Ajouté

    public function __construct(
        EntityManagerInterface $em,
        DepartementRepository $deptRepo,
        UtilisateurRepository $userRepo, // Injecté
        HubInterface $hub               // Injecté
    ) {
        $this->em = $em;
        $this->deptRepo = $deptRepo;
        $this->userRepo = $userRepo;
        $this->hub = $hub;
    }

    public function createFullRegistration(array $data): Visite
    {
        $departement = $this->deptRepo->find($data['departementId']);

        if (!$departement) {
            throw new \Exception("Département introuvable");
        }

        $visiteur = new Visiteur();
        $visiteur->setNom($data['nom']);
        $visiteur->setPrenom($data['prenom']);
        $visiteur->setEmail($data['email']);
        $visiteur->setTelephone($data['telephone']);
        $visiteur->setLycee($data['lycee']);
        $visiteur->setVille($data['ville']);
        $visiteur->setDepartement($departement);

        $this->em->persist($visiteur);

        $visite = new Visite();
        $visite->setVisiteur($visiteur);
        $visite->setDepartement($departement);
        $visite->setStatut('ATTENTE');

        // CORRECTION ICI : On utilise setDebut() car c'est le nom dans ton entité
        $visite->setDebut(new \DateTime());

        // TRÈS IMPORTANT : Comme tes colonnes visiteur_id et etudiant_id 
        // ne sont pas nullables dans ton entité, on doit leur donner une valeur
        // même si on utilise déjà les objets $visiteur et $etudiant.
        $visite->setVisiteurId(0);
        $visite->setEtudiantId(0);

        $this->em->persist($visite);
        $this->em->flush();

        // On lance la notification Mercure dès que l'inscription est finie
        //$this->notifyStudents($departement);

        return $visite;
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

        // 1. Mise à jour de la visite
        $visite->setStatut('EN_COURS');
        $visite->setEtudiant($etudiant);
        $visite->setEtudiantId($etudiant->getId());

        // 2. Mise à jour de l'étudiant
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

        // Terminer la visite
        $visite->setStatut('TERMINE');
        $visite->setFin(new \DateTime()); // On enregistre l'heure exacte de fin

        // Libérer l'étudiant
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

        // On récupère les visites en attente (ordonnées par heure d'arrivée)
        $visites = $this->em->getRepository(\App\Entity\Visite::class)->findBy([
            'departement' => $departement,
            'statut' => 'ATTENTE'
        ], ['debut' => 'ASC']);

        // On formate pour le binôme Next.js
        return array_map(fn($v) => [
            'visiteId' => $v->getId(),
            'nom' => $v->getVisiteur()->getNom(),
            'prenom' => $v->getVisiteur()->getPrenom(),
            'heureArrivee' => $v->getDebut()->format('H:i')
        ], $visites);
    }
}
