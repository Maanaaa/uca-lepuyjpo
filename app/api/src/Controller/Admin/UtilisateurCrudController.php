<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurCrudController extends AbstractFilterableCrudController 
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public static function getEntityFqcn(): string 
    { 
        return Utilisateur::class; 
    }

    public function configureFields(string $pageName): iterable
    {
        yield EmailField::new('email');
        yield TextField::new('nom');
        yield TextField::new('prenom');
        yield AssociationField::new('departement')->setRequired(true);
        yield BooleanField::new('isDisponible', 'Disponible');
        yield ChoiceField::new('roles')
            ->allowMultipleChoices()
            ->renderAsBadges()
            ->setChoices([
                'Administrateur Département' => 'ROLE_DEPT_ADMIN',
                'Étudiant' => 'ROLE_USER',
                'Super Administrateur' => 'ROLE_SUPER_ADMIN',
            ]);

        yield TextField::new('password', 'Mot de passe (Admin uniquement)')
            ->onlyOnForms()
            ->setRequired(false)
            ->setHelp('Laissez vide pour un étudiant (connexion prenom.nom)');
    }

    public function createEntity(string $entityFqcn): Utilisateur
    {
        $user = new Utilisateur();
        /** @var Utilisateur $currentUser */
        $currentUser = $this->getUser();

        if ($currentUser && !in_array('ROLE_SUPER_ADMIN', $currentUser->getRoles())) {
            $user->setIsDisponible(true); 
            $user->setDepartement($currentUser->getDepartement());
        }

        return $user;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Utilisateur) {
            if (!$entityInstance->getPassword()) {
                $plainPassword = strtolower($entityInstance->getPrenom() . '.' . $entityInstance->getNom());
            } else {
                $plainPassword = $entityInstance->getPassword();
            }

            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashedPassword);
        }

        parent::persistEntity($entityManager, $entityInstance);
    }
}