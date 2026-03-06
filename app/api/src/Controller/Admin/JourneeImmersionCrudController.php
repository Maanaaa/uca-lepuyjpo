<?php

namespace App\Controller\Admin;

use App\Entity\JourneeImmersion;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class JourneeImmersionCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return JourneeImmersion::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
    return $crud
        ->setEntityLabelInPlural('Journées d\'immersion')
        ->setEntityLabelInSingular('Journée d\'immersion')

        ->setPageTitle('index', 'Gestion des %entity_label_plural%')
        ->setPageTitle('new', 'Créer une %entity_label_singular%');
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        
        yield DateField::new('date', 'Date de la journée');

        // affichage du département selon le rôle
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield AssociationField::new('departement', 'Département rattaché');
        } else {
            // Pour l'admin de département, on peut l'afficher en lecture seule
            yield AssociationField::new('departement')
                ->setFormTypeOption('disabled', true)
                ->hideOnForm(); 
        }
    }
}