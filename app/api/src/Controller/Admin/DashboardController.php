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
            ->addHtmlContentToBody('<script>
                const VAPID_PUBLIC_KEY = "' . $_ENV['VAPID_PUBLIC_KEY'] . '";
                const MERCURE_URL = "http://localhost:4444/.well-known/mercure";
                const MERCURE_TOPIC = "https://jpo.uca.fr/visites";

                // --- TES FONCTIONS DE BOUTONS (OK) ---
                function forceAccept(btn) {
                    let vId = btn.getAttribute("data-visite-id");
                    if (vId === "__entity_id__" || !vId) {
                        const row = btn.closest("tr");
                        vId = row ? row.getAttribute("data-id") : null;
                    }
                    const eId = btn.getAttribute("data-etudiant-id");
                    if (!vId || vId === "__entity_id__") return alert("ID manquant");

                    btn.innerHTML = \'<i class="fa fa-spin fa-spinner"></i>\';
                    btn.classList.add("action-busy"); // Empêche le refresh Mercure

                    fetch("/api/visite/accept", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ visiteId: parseInt(vId), etudiantId: parseInt(eId) })
                    }).then(() => window.location.reload());
                }

                function forceFinish(btn) {
                    let vId = btn.getAttribute("data-visite-id");
                    if (vId === "__entity_id__" || !vId) {
                        const row = btn.closest("tr");
                        vId = row ? row.getAttribute("data-id") : null;
                    }
                    if (!vId || vId === "__entity_id__" || !confirm("Terminer ?")) return;

                    btn.innerHTML = \'<i class="fa fa-spin fa-spinner"></i>\';
                    btn.classList.add("action-busy");

                    fetch("/api/visite/finish", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ visiteId: parseInt(vId) })
                    }).then(() => window.location.reload());
                }

                // --- NOTIFS & SERVICE WORKER ---
                if ("serviceWorker" in navigator) navigator.serviceWorker.register("/sw.js");

                function urlBase64ToUint8Array(base64String) {
                    const padding = "=".repeat((4 - base64String.length % 4) % 4);
                    const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
                    const rawData = window.atob(base64);
                    const outputArray = new Uint8Array(rawData.length);
                    for (let i = 0; i < rawData.length; ++i) { outputArray[i] = rawData.charCodeAt(i); }
                    return outputArray;
                }

                async function subscribeToNotifications() {
                    try {
                        const reg = await navigator.serviceWorker.ready;
                        const sub = await reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(VAPID_PUBLIC_KEY)
                        });
                        await fetch("/api/push-subscribe", {
                            method: "POST",
                            body: JSON.stringify(sub),
                            headers: { "Content-Type": "application/json" }
                        });
                        alert("Alertes actives !");
                    } catch (e) { console.error(e); }
                }

                // --- MERCURE REFRESH ---
                const url = new URL(MERCURE_URL);
                url.searchParams.append("topic", MERCURE_TOPIC);
                const eventSource = new EventSource(url);

                eventSource.onmessage = event => {
                    // On ne refresh pas si un bouton forceAccept/Finish est en cours
                    if (document.querySelector(".action-busy")) return;

                    const data = JSON.parse(event.data);
                    if (data.type === "NEW_VISITOR") {
                        if (window.location.href.toLowerCase().includes("visite")) {
                            window.location.reload();
                        }
                    }
                };
            </script>');
    }

public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Accueil', 'fa fa-home');
        yield MenuItem::linkToUrl('Alertes 🔔', 'fa fa-bell', 'javascript:subscribeToNotifications()');

        yield MenuItem::section('Structure')->setPermission('ROLE_DEPT_ADMIN');
        
        yield MenuItem::linkToUrl('Départements', 'fa fa-building', 
            $this->adminUrlGenerator->setController(DepartementCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Cours', 'fa fa-book', 
            $this->adminUrlGenerator->setController(CoursCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Immersion', 'fa fa-calendar', 
            $this->adminUrlGenerator->setController(JourneeImmersionCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Utilisateurs', 'fa fa-user', 
            $this->adminUrlGenerator->setController(UtilisateurCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');


        yield MenuItem::linkToUrl('Inscriptions Immersion', 'fa fa-list', 
            $this->adminUrlGenerator
                ->setController(InscriptionImmersionCrudController::class)
                ->setAction('index')
                ->generateUrl()
            )
            ->setPermission('ROLE_DEPT_ADMIN');

        yield MenuItem::section('Flux');
        
        yield MenuItem::linkToUrl('Visiteurs', 'fa fa-user-friends', 
            $this->adminUrlGenerator->setController(VisiteurCrudController::class)->setAction('index')->generateUrl())
            ->setPermission('ROLE_DEPT_ADMIN');
            
        yield MenuItem::linkToUrl('Visites', 'fa fa-clock', 
            $this->adminUrlGenerator->setController(VisiteCrudController::class)->setAction('index')->generateUrl());
    }
}