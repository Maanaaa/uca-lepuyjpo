<?php

namespace App\Controller\Admin;

use App\Entity\Cours;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TimeField;

class CoursCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cours::class;
    }

    public function configureFields(string $pageName): iterable
    { 
        yield TextField::new('matiere', 'Nom de la matière');

        yield AssociationField::new('departement', 'Département');
        
        // Attention : dans ton entité c'est 'journeeImmersion' (camelCase)
        yield AssociationField::new('journeeImmersion', 'Journée d\'immersion')
            ->setRequired(true);
            
    }
}