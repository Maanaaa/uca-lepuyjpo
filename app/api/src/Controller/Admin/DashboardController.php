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

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);

        if (!$this->isGranted('ROLE_DEPT_ADMIN')) {
            return $this->redirect($adminUrlGenerator->setController(VisiteCrudController::class)->setAction('index')->generateUrl());
        }

        return $this->redirect($adminUrlGenerator->setController(UtilisateurCrudController::class)->setAction('index')->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('JPO Admin')
            ->renderContentMaximized();
    }

    public function configureAssets(): Assets
    {
        $customJs = <<<JS
        function forceAccept(btn) {
            // 1. On essaie de récupérer l'ID via l'attribut data
            let vId = btn.getAttribute("data-visite-id");
            
            // 2. HACK : Si EA n'a pas remplacé le placeholder, on cherche l'ID dans la ligne (tr)
            if (vId === "__entity_id__" || !vId) {
                const row = btn.closest('tr');
                // EasyAdmin stocke souvent l'ID dans data-id ou une cellule spécifique
                vId = row ? row.getAttribute('data-id') : null;
            }

            const eId = btn.getAttribute("data-etudiant-id");

            console.log("Tentative d'acceptation - Visite ID:", vId, "Etudiant ID:", eId);

            if (!vId || vId === "__entity_id__") {
                alert("Erreur : Impossible de récupérer l'ID de la visite. Essayez de rafraîchir.");
                return;
            }

            btn.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
            btn.style.pointerEvents = "none";

            fetch("/api/visite/accept", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    visiteId: parseInt(vId),
                    etudiantId: parseInt(eId)
                })
            })
            .then(async response => {
                if (response.ok) {
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert("Erreur API : " + (data.error || "Problème lors de l'acceptation"));
                    window.location.reload();
                }
            })
            .catch(err => {
                console.error("Erreur réseau :", err);
                alert("Impossible de joindre l'API (Vérifie ton serveur)");
            });
        }
    function forceFinish(btn) {
        let vId = btn.getAttribute("data-visite-id");
        
        // Ruse pour récupérer l'ID si EA fait de la résistance
        if (vId === "__entity_id__" || !vId) {
            const row = btn.closest('tr');
            vId = row ? row.getAttribute('data-id') : null;
        }

        if (!vId || vId === "__entity_id__") {
            alert("Erreur : Impossible de récupérer l'ID.");
            return;
        }

        if(!confirm("Voulez-vous terminer cette visite ?")) return;

        btn.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';

        fetch("/api/visite/finish", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ visiteId: parseInt(vId) })
        })
        .then(async response => {
            if (response.ok) {
                window.location.reload();
            } else {
                const data = await response.json();
                alert("Erreur : " + (data.error || "Inconnue"));
                window.location.reload();
            }
        })
        .catch(err => alert("Erreur réseau : " + err));
    }
JS;

        return parent::configureAssets()
            ->addHtmlContentToBody('<script>' . $customJs . '</script>');
    }

    public function configureMenuItems(): iterable
    {   
        yield MenuItem::section('Structure');
        yield MenuItem::linkTo(Departement::class, 'Départements', 'fa fa-building')->setAction('index');
        yield MenuItem::linkTo(Cours::class, 'Cours', 'fa fa-book')->setAction('index');
        yield MenuItem::linkTo(JourneeImmersion::class, "Journées d'immersion", 'fa fa-calendar')->setAction('index');
        yield MenuItem::linkTo(Utilisateur::class, "Utilisateurs", 'fa fa-user')->setAction('index');
        
        yield MenuItem::section('Flux');
        yield MenuItem::linkTo(Visiteur::class, 'Visiteurs', 'fa fa-user-friends')->setAction('index');
        yield MenuItem::linkTo(Visite::class, 'Visites', 'fa fa-clock')->setAction('index');
    }
}