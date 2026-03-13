<?php

namespace App\Controller\Admin;

use App\Entity\Departement;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use Vich\UploaderBundle\Form\Type\VichFileType;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;

class DepartementCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Departement::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('nom'),
            SlugField::new('slug')->setTargetFieldName('nom'),

            ImageField::new('nomPdf', 'Voir le PDF')
                ->setBasePath('uploads/departements') 
                ->onlyOnIndex(),

            Field::new('pdfFile', 'Document PDF')
                ->setFormType(VichFileType::class)
                ->onlyOnForms()
                ->setFormTypeOptions([
                    'allow_delete' => true,
                    'download_uri' => true,
                    'download_label' => 'Télécharger le PDF',
                ]),
        ];
    }
}