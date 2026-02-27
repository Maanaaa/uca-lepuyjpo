<?php

namespace App\DataFixtures;

use App\Entity\Departement;
use App\Entity\Etudiant;
use App\Entity\JourneeImmersion;
use App\Entity\Utilisateur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $mmi = new Departement();
        $mmi->setNom('MMI');
        $mmi->setSlug('mmi');
        $manager->persist($mmi);

        $info = new Departement();
        $info->setNom('Informatique');
        $info->setSlug('info');
        $manager->persist($info);

        $ji = new JourneeImmersion();
        $ji->setDate(new \DateTime('2026-03-15'));
        $ji->setDepartement($mmi);
        $manager->persist($ji);

        $user = new Utilisateur();
        $user->setEmail('etudiant@mmi.fr');
        $user->setNom('Manya');
        $user->setPrenom('Théo');
        $user->setRoles(['ROLE_ETUDIANT']);
        // Mot de passe : password
        $user->setPassword($this->hasher->hashPassword($user, 'password'));
        $manager->persist($user);

        $etudiant = new Etudiant();
        $etudiant->setStatut('DISPONIBLE'); 
        $etudiant->setDepartement($mmi);

        $manager->persist($etudiant);

        $manager->flush();
    }
}