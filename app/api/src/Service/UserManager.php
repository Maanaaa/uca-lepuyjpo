<?php

namespace App\Service;

use App\Entity\Utilisateur;
use App\Repository\DepartementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private DepartementRepository $deptRepo,
        private UserPasswordHasherInterface $passwordHasher 
    ) {}

    public function createStudent(array $data): Utilisateur
    {
        $departement = $this->deptRepo->find($data['departementId']);
        if (!$departement) {
            throw new \Exception("Département introuvable");
        }

        $student = new Utilisateur();
        
        // Génération de la base : prenom.nom
        $baseIdentifiant = strtolower($data['prenom'] . '.' . $data['nom']);
        $baseIdentifiant = str_replace(' ', '', $baseIdentifiant); 

        // L'email devient prenom.nom@etu.uca.fr
        $email = $baseIdentifiant . '@etu.uca.fr';

        $student->setEmail($email); 
        $student->setNom($data['nom']);
        $student->setPrenom($data['prenom']);
        $student->setRoles(['ROLE_USER']);
        $student->setIsDisponible(true);
        $student->setDepartement($departement);
        
        // Le mot de passe devient "prenom.nom" haché
        $hashedPassword = $this->passwordHasher->hashPassword($student, $baseIdentifiant);
        $student->setPassword($hashedPassword);

        $this->em->persist($student);
        $this->em->flush();

        return $student;
    }
}