<?php

namespace App\Controller\Admin;

use App\Entity\Visiteur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;

class VisiteurCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return Visiteur::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom');
        yield TextField::new('prenom');
        yield TextField::new('lycee', 'Lycée d\'origine');
        yield TextField::new('ville');

        // Ajout du département
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield AssociationField::new('departement', 'Département');
        } else {
            // En index on le montre, en formulaire on le cache (auto-rempli)
            yield AssociationField::new('departement')->hideOnForm();
        }
    }
    

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
