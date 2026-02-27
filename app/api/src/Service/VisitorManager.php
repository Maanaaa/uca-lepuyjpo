<?php
namespace App\Service;

use App\Entity\Visiteur;
use App\Entity\Visite;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;

class VisitorManager
{
    private $em;
    private $deptRepo;

    public function __construct(EntityManagerInterface $em, DepartementRepository $deptRepo)
    {
        $this->em = $em;
        $this->deptRepo = $deptRepo;
    }

    public function createFullRegistration(array $data): Visite
    {   
        $departement = $this->deptRepo->find($data['departementId']);
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

        $this->em->persist($visite);
        
        $this->em->flush();

        return $visite;
    }
}