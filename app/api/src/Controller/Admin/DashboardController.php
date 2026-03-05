<?php

namespace App\Controller\Admin;

use App\Controller\Admin\DepartementCrudController;
use App\Controller\Admin\UtilisateurCrudController;
use App\Controller\Admin\VisiteCrudController;
use App\Controller\Admin\VisiteurCrudController;
use App\Entity\JourneeImmersion;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return parent::index();
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JPO Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');

        yield MenuItem::section('Structure');
        // NOUVEAUTÉ V5 : On utilise linkTo() avec le Controller
        yield MenuItem::linkTo(DepartementCrudController::class, 'Départements', 'fa fa-building');
        yield MenuItem::linkTo(CoursCrudController::class, 'Cours', 'fa fa-book');
        yield MenuItem::linkTo(JourneeImmersionCrudController::class, "Journées d'imersion", 'fa fa-calendar');
        yield MenuItem::linkTo(UtilisateurCrudController::class, "Utilisateurs", 'fa fa-user');
        yield MenuItem::section('Flux');
        yield MenuItem::linkTo(VisiteurCrudController::class, 'Visiteurs', 'fa fa-user');
        yield MenuItem::linkTo(VisiteCrudController::class, 'Visites', 'fa fa-clock');
       
    }
}