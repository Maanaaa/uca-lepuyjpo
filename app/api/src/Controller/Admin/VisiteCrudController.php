<?php

namespace App\Controller\Admin;

use App\Entity\Visite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;


class VisiteCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string
    {
        return Visite::class;
    }

    // Pour notifier les étudiants
    public function configureActions(Actions $actions): Actions
    {
    $notifyAction = Action::new('notifyStudent', 'Notifier Étudiant', 'fa fa-bell')
        ->linkToCrudAction('notifyStudentAction') 
        ->displayIf(fn ($entity) => $entity->getStatut() === 'ATTENTE');

    return $actions
        ->add(Crud::PAGE_INDEX, $notifyAction);
    }


    public function notifyStudentAction(AdminContext $context, AdminUrlGenerator $adminUrlGenerator): Response
    {
        $visite = $context->getEntity()->getInstance();

        $this->addFlash('success', 'Notification envoyée pour ' . $visite->getVisiteur());

        $url = $adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->generateUrl();
        return $this->redirect($url);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        

        yield AssociationField::new('visiteur', 'Visiteur');
        
        // Relation avec l'étudiant qui fait la visite
        yield AssociationField::new('etudiant', 'Étudiant Guide');

        yield ChoiceField::new('statut')->setChoices([
            'En attente' => 'ATTENTE',
            'En cours' => 'EN_COURS',
            'Terminé' => 'TERMINE',
        ]);

        yield DateTimeField::new('heureArrivee', 'Arrivée');

        // Ajout du département rattaché
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield AssociationField::new('departement', 'Département');
        } else {
            yield AssociationField::new('departement')->hideOnForm();
        }
    }
}
