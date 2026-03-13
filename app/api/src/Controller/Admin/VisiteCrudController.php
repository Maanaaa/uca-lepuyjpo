<?php

namespace App\Controller\Admin;

use App\Entity\Visite;
use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class VisiteCrudController extends AbstractFilterableCrudController
{
    public static function getEntityFqcn(): string { return Visite::class; }

    /**
     * FILTRE POUR LES ÉTUDIANTS
     */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $user = $this->getUser();

        // Si c'est l'admin du département, il voit tout
        if ($this->isGranted('ROLE_DEPT_ADMIN')) {
            return $qb;
        }

        // Pour l'étudiant : Visites en ATTENTE (libres) OU SES visites EN_COURS
        $qb->andWhere('entity.statut = :attente OR (entity.statut = :enCours AND entity.etudiant = :me)')
           ->setParameter('attente', 'ATTENTE')
           ->setParameter('enCours', 'EN_COURS')
           ->setParameter('me', $user);

        return $qb;
    }

    public function configureActions(Actions $actions): Actions
    {
        $user = $this->getUser();
        $userId = ($user instanceof Utilisateur) ? $user->getId() : 0;

        // ACTION ACCEPTER (JS)
        $acceptAction = Action::new('acceptVisite', 'Accepter', 'fa fa-check')
            ->linkToUrl('#')
            ->setHtmlAttributes([
                'onclick' => 'forceAccept(this)',
                'data-visite-id' => '__entity_id__', 
                'data-etudiant-id' => $userId,
            ])
            ->addCssClass('btn btn-success')
            ->displayIf(fn ($entity) => $entity->getStatut() === 'ATTENTE' && !$this->isGranted('ROLE_DEPT_ADMIN'));

        // ACTION TERMINER (JS)
        $finishAction = Action::new('finishVisite', 'Terminer', 'fa fa-qrcode')
            ->linkToUrl('#')
            ->setHtmlAttributes([
                'onclick' => 'forceFinish(this)',
                'data-visite-id' => '__entity_id__',
            ])
            ->addCssClass('btn btn-danger')
            ->displayIf(fn ($entity) => $entity->getStatut() === 'EN_COURS' && $entity->getEtudiant() === $user);

        return $actions
            ->add(Crud::PAGE_INDEX, $acceptAction)
            ->add(Crud::PAGE_INDEX, $finishAction)
            ->update(Crud::PAGE_INDEX, Action::EDIT, fn(Action $a) => $a->displayIf(fn() => $this->isGranted('ROLE_DEPT_ADMIN')))
            ->update(Crud::PAGE_INDEX, Action::DELETE, fn(Action $a) => $a->displayIf(fn() => $this->isGranted('ROLE_DEPT_ADMIN')));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->onlyOnIndex();
        yield AssociationField::new('visiteur');
        yield AssociationField::new('etudiant', 'Guide');
        yield DateTimeField::new('debut', 'Arrivée');
        yield ChoiceField::new('statut')->setChoices([
            'En attente' => 'ATTENTE', 
            'En cours' => 'EN_COURS', 
            'Terminé' => 'TERMINE'
        ]);
    }
}