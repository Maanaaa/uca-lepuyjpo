<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private DepartementRepository $deptRepo
    ) {}

    public function createStudent(array $data): Utilisateur
    {
        $departement = $this->deptRepo->find($data['departementId']);
        if (!$departement) {
            throw new \Exception("Département introuvable");
        }

        $student = new Utilisateur();
        
        // Génération de l'identifiant unique : prenom.nom
        $identifiant = strtolower($data['prenom'] . '.' . $data['nom']);
        $identifiant = str_replace(' ', '', $identifiant); 

        $student->setEmail($identifiant); // L'identifiant sert de login
        $student->setNom($data['nom']);
        $student->setPrenom($data['prenom']);
        $student->setRoles(['ROLE_USER']);
        $student->setIsDisponible(true);
        $student->setDepartement($departement);
        $student->setPassword('none'); // Pas de mdp pour le login tablette étudiant

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }
}