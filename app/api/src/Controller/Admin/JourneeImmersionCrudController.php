<?php

namespace App\Controller\Admin;

use App\Entity\JourneeImmersion;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;

class JourneeImmersionCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return JourneeImmersion::class;
    }

    public function configureFields(string $pageName): iterable
    {
        // On affiche l'ID uniquement sur la page liste (Index)
        yield IdField::new('id')->hideOnForm();
        
        yield TextField::new('titre', 'Nom de la journée');
        
        yield DateTimeField::new('dateDebut', 'Début de l\'immersion');
        
        yield IntegerField::new('capacite', 'Nombre de places');

        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield AssociationField::new('departement', 'Département concerné')
                ->setRequired(true);
        } else {
            yield AssociationField::new('departement')
                ->hideOnForm();
        }

        yield TextEditorField::new('description')->hideOnIndex();
    }
}