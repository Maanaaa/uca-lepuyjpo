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
        $script = <<<JS
        <script>
        window.forceAccept = function(btn) {
            let vId = btn.getAttribute("data-visite-id");
            if (!vId || vId === "__entity_id__") {
                const row = btn.closest('tr');
                vId = row ? row.getAttribute('data-id') : null;
            }
            const eId = btn.getAttribute("data-etudiant-id");
            if (!vId) return;

            btn.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
            fetch("/api/visite/accept", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ visiteId: parseInt(vId), etudiantId: parseInt(eId) })
            }).then(() => window.location.reload());
        };

        window.forceFinish = function(btn) {
            let vId = btn.getAttribute("data-visite-id");
            if (!vId || vId === "__entity_id__") {
                const row = btn.closest('tr');
                vId = row ? row.getAttribute('data-id') : null;
            }
            if(!vId || !confirm("Terminer la visite ?")) return;

            btn.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
            fetch("/api/visite/finish", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ visiteId: parseInt(vId) })
            }).then(() => window.location.reload());
        };
        </script>
JS;
        return parent::configureAssets()->addHtmlContentToBody($script);
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');

        // Visible que pour les admins
        yield MenuItem::section('Structure')->setPermission('ROLE_DEPT_ADMIN');
        
        yield MenuItem::linkToUrl('Départements', 'fa fa-building', 
            $this->adminUrlGenerator->setController(DepartementCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Cours', 'fa fa-book', 
            $this->adminUrlGenerator->setController(CoursCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Journées d\'immersion', 'fa fa-calendar', 
            $this->adminUrlGenerator->setController(JourneeImmersionCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Utilisateurs', 'fa fa-user', 
            $this->adminUrlGenerator->setController(UtilisateurCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');

        // Visible par tout le monde
        yield MenuItem::section('Flux');
        
        yield MenuItem::linkToUrl('Visiteurs', 'fa fa-user-friends', 
            $this->adminUrlGenerator->setController(VisiteurCrudController::class)->setAction('index')->generateUrl());
            
        yield MenuItem::linkToUrl('Visites', 'fa fa-clock', 
            $this->adminUrlGenerator->setController(VisiteCrudController::class)->setAction('index')->generateUrl());
    }
}