<?php
namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Fields;
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
        
        // On affiche l'email mais on le rend facultatif au remplissage 
        // car on va le générer nous-mêmes
        yield TextField::new('email')->hideOnForm(); 

        yield AssociationField::new('departement');
        
        yield ChoiceField::new('roles')
            ->setChoices(['Étudiant' => 'ROLE_USER', 'Admin' => 'ROLE_DEPT_ADMIN'])
            ->allowMultipleChoices()
            ->renderAsBadges();


    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Utilisateur) return;

        // Génération prenom.nom
        $base = strtolower($entityInstance->getPrenom() . '.' . $entityInstance->getNom());
        $base = str_replace(' ', '', $base);

        // email
        $entityInstance->setEmail($base . '@etu.uca.fr');

        // psswd auto (haché)
        $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $base);
        $entityInstance->setPassword($hashedPassword);

        $entityInstance->setIsDisponible(true);

        parent::persistEntity($entityManager, $entityInstance);
    }
}