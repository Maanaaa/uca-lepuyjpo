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


class VisiteCrudController extends AbstractCrudController
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
