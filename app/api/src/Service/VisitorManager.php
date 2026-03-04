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

    public function notifyStudents(Departement $dept): void
    {
        // On cherche les étudiants dispos dans ce département via UtilisateurRepository
        $dispos = $this->userRepo->findBy(['departement' => $dept, 'isDisponible' => true]);

        $topic = "http://localhost:8080/api/notifications/" . $dept->getSlug();
        
        $payload = [
            'type' => 'NEW_VISITOR',
            'dept' => $dept->getNom(),
            'timestamp' => date('H:i')
        ];

        if (empty($dispos)) {
            $payload['message'] = 'ALERTE : Aucun étudiant disponible !';
            $payload['urgent'] = true;
        } else {
            $payload['message'] = 'Un nouveau visiteur attend en ' . $dept->getNom();
            $payload['urgent'] = false;
        }

        $update = new Update($topic, json_encode($payload));
        $this->hub->publish($update);
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
}