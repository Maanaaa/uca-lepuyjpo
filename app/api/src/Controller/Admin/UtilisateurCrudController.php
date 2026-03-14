<?php
namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UtilisateurCrudController extends AbstractCrudController
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
        yield TextField::new('prenom', 'Prénom');
        yield TextField::new('nom', 'Nom');
        
        yield TextField::new('email', 'Email (Laisse vide pour étudiant)')
            ->setRequired(false); // Champ non-obligatoire

        yield TextField::new('password', 'Mot de passe (Laisse vide pour étudiant)')
            ->onlyOnForms()
            ->setRequired(false);

        yield AssociationField::new('departement');
        
        yield ChoiceField::new('roles')
            ->setChoices([
                'Étudiant' => 'ROLE_USER',
                'Admin Département' => 'ROLE_DEPT_ADMIN'
            ])
            ->allowMultipleChoices()
            ->renderAsBadges();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Utilisateur) return;

        $roles = $entityInstance->getRoles();
        $plainPassword = $entityInstance->getPassword();

        // CAS ÉTUDIANT : Si le mdp est vide ou si c'est un ROLE_USER
        if (in_array('ROLE_USER', $roles) && empty($plainPassword)) {
            $base = strtolower($entityInstance->getPrenom() . '.' . $entityInstance->getNom());
            $base = str_replace(' ', '', $base);

            if (empty($entityInstance->getEmail())) {
                $entityInstance->setEmail($base . '@etu.uca.fr');
            }

            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $base);
            $entityInstance->setPassword($hashedPassword);
        } 
        // CAS ADMIN : Si un mot de passe a été saisi manuellement
        elseif (!empty($plainPassword)) {
            $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $plainPassword);
            $entityInstance->setPassword($hashedPassword);
        }

        $entityInstance->setIsDisponible(true);

        parent::persistEntity($entityManager, $entityInstance);
    }
}