<?php
namespace App\Controller\Admin;

use App\Entity\InscriptionImmersion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;

class InscriptionImmersionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InscriptionImmersion::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Inscription Immersion')
            ->setEntityLabelInPlural('Inscriptions Immersion')
            ->setDefaultSort(['inscritLe' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        // LISTE 
        yield TextField::new('visiteur.prenom', 'Prénom');
        yield TextField::new('visiteur.nom', 'Nom');
        yield EmailField::new('visiteur.email', 'Email');
        yield AssociationField::new('journeeImmersion', 'Journée');
        yield DateTimeField::new('inscritLe', 'Inscrit le');
        
        // DÉTAIL
        if ($pageName === 'detail') {
            yield AssociationField::new('visiteur');
            yield AssociationField::new('journeeImmersion');
            yield DateTimeField::new('inscritLe');
        }
    }
}
