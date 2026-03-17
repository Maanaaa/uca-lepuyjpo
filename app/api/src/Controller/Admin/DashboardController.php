<?php

namespace App\Controller\Admin;

use App\Entity\Utilisateur;
use App\Entity\Visite;
use App\Entity\Visiteur;
use App\Entity\Departement;
use App\Entity\JourneeImmersion;
use App\Entity\Cours;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Controller\Admin\InscriptionImmersionCrudController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    private $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }

    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        if (!$this->isGranted('ROLE_DEPT_ADMIN')) {
            return $this->redirect($this->adminUrlGenerator->setController(VisiteCrudController::class)->setAction('index')->generateUrl());
        }
        return $this->redirect($this->adminUrlGenerator->setController(UtilisateurCrudController::class)->setAction('index')->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JPO Admin')
            ->renderContentMaximized();
    }

    public function configureAssets(): Assets
    {
        
        return parent::configureAssets()
            ->addJsFile('https://cdn.jsdelivr.net/npm/sweetalert2@11')
            ->addJsFile('https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js')
            ->addJsFile('js/admin_custom.js')
            ->addHtmlContentToBody(sprintf(
                '<div id="js-config" data-vapid-key="%s" data-mercure-url="%s" data-mercure-topic="%s" style="display:none;"></div>',
                $_ENV['VAPID_PUBLIC_KEY'],
                'http://localhost:4444/.well-known/mercure',
                'https://jpo.uca.fr/visites'
            ));
    }

    

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        yield MenuItem::linkToUrl('Alertes 🔔', 'fa fa-bell', 'javascript:subscribeToNotifications()');

        yield MenuItem::section('Structure')->setPermission('ROLE_DEPT_ADMIN');


        yield MenuItem::linkToUrl(
            'Statistiques',
            'fa fa-chart-bar',
            'http://localhost:3000/statistics'
        );

        yield MenuItem::linkToUrl(
            'Départements',
            'fa fa-building',
            $this->adminUrlGenerator->setController(DepartementCrudController::class)->setAction('index')->generateUrl()
        )
            ->setPermission('ROLE_DEPT_ADMIN');

        yield MenuItem::linkToUrl(
            'Cours',
            'fa fa-book',
            $this->adminUrlGenerator->setController(CoursCrudController::class)->setAction('index')->generateUrl()
        )
            ->setPermission('ROLE_DEPT_ADMIN');

        yield MenuItem::linkToUrl(
            'Immersion',
            'fa fa-calendar',
            $this->adminUrlGenerator->setController(JourneeImmersionCrudController::class)->setAction('index')->generateUrl()
        )
            ->setPermission('ROLE_DEPT_ADMIN');

        yield MenuItem::linkToUrl(
            'Utilisateurs',
            'fa fa-user',
            $this->adminUrlGenerator->setController(UtilisateurCrudController::class)->setAction('index')->generateUrl()
        )
            ->setPermission('ROLE_DEPT_ADMIN');


        yield MenuItem::linkToUrl(
            'Inscriptions Immersion',
            'fa fa-list',
            $this->adminUrlGenerator
                ->setController(InscriptionImmersionCrudController::class)
                ->setAction('index')
                ->generateUrl()
        )
            ->setPermission('ROLE_DEPT_ADMIN');

        yield MenuItem::section('Flux');

        yield MenuItem::linkToUrl(
            'Visiteurs',
            'fa fa-user-friends',
            $this->adminUrlGenerator->setController(VisiteurCrudController::class)->setAction('index')->generateUrl()
        )
            ->setPermission('ROLE_DEPT_ADMIN');

        yield MenuItem::linkToUrl(
            'Visites',
            'fa fa-clock',
            $this->adminUrlGenerator->setController(VisiteCrudController::class)->setAction('index')->generateUrl()
        );
    }
}
