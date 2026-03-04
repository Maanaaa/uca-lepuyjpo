<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use App\Entity\JourneeImmersion;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        // --- CRÉATION DES DÉPARTEMENTS ---
        $mmi = new Departement();
        $mmi->setNom('MMI');
        $mmi->setSlug('mmi');
        $manager->persist($mmi);

        $info = new Departement();
        $info->setNom('Informatique');
        $info->setSlug('info');
        $manager->persist($info);

        $infoCom = new Departement();
        $infoCom->setNom('Information-Communication');
        $infoCom->setSlug('infocom');
        $manager->persist($infoCom);

        // --- JOURNÉE D'IMMERSION ---
        $ji = new JourneeImmersion();
        $ji->setDate(new \DateTime('2026-03-15'));
        $ji->setDepartement($mmi);
        $manager->persist($ji);

        // --- UTILISATEURS / ÉTUDIANTS ---

        // 1. Théo (MMI - Disponible)
        $user1 = new Utilisateur();
        $user1->setEmail('etudiant@mmi.fr');
        $user1->setNom('Manya');
        $user1->setPrenom('Théo');
        $user1->setRoles(['ROLE_ETUDIANT']);
        $user1->setDepartement($mmi);
        $user1->setIsDisponible(true);
        $user1->setPassword($this->hasher->hashPassword($user1, 'password'));
        $manager->persist($user1);

        // 2. Lucas (MMI - Disponible)
        $user2 = new Utilisateur();
        $user2->setEmail('lucas.mmi@uca.fr');
        $user2->setNom('Bernard');
        $user2->setPrenom('Lucas');
        $user2->setRoles(['ROLE_ETUDIANT']);
        $user2->setDepartement($mmi);
        $user2->setIsDisponible(true);
        $user2->setPassword($this->hasher->hashPassword($user2, 'password'));
        $manager->persist($user2);

        // 3. Bob (Informatique - OCCUPÉ)
        $user3 = new Utilisateur();
        $user3->setEmail('info.occupe@uca.fr');
        $user3->setNom('Durand');
        $user3->setPrenom('Bob');
        $user3->setRoles(['ROLE_ETUDIANT']);
        $user3->setDepartement($info);
        $user3->setIsDisponible(false); // Simule une visite en cours
        $user3->setPassword($this->hasher->hashPassword($user3, 'password'));
        $manager->persist($user3);

        // 4. Julie (InfoCom - Disponible)
        $user4 = new Utilisateur();
        $user4->setEmail('infocom.dispo@uca.fr');
        $user4->setNom('Petit');
        $user4->setPrenom('Julie');
        $user4->setRoles(['ROLE_ETUDIANT']);
        $user4->setDepartement($infoCom);
        $user4->setIsDisponible(true);
        $user4->setPassword($this->hasher->hashPassword($user4, 'password'));
        $manager->persist($user4);

        // 5. Admin (Général)
        $admin = new Utilisateur();
        $admin->setEmail('admin@uca.fr');
        $admin->setNom('Responsable');
        $admin->setPrenom('Admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsDisponible(false);
        $admin->setPassword($this->hasher->hashPassword($admin, 'admin123'));
        $manager->persist($admin);

        $manager->flush();
    }
}