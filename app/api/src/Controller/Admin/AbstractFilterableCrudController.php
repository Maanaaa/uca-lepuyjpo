<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractFilterableCrudController extends AbstractCrudController
{
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $qb = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        
        $user = $this->getUser();

        if ($user && !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            $class = $entityDto->getFqcn();
            if (method_exists($class, 'getDepartement')) {
                /** @var Utilisateur|null $user */
                $qb->andWhere('entity.departement = :dept')
                   ->setParameter('dept', $user->getDepartement());
            }
        }

        return $qb;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function createEntity(string $entityFqcn): object
    {
        $entity = new $entityFqcn();
        /** @var Utilisateur|null $user */
        $user = $this->getUser();

        if ($user && !in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            if (method_exists($entity, 'setDepartement')) {
                $entity->setDepartement($user->getDepartement());
            }
        }

        return $entity;
    }

}