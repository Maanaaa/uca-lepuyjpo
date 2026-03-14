const config = {
    // Pour les notifs
    vapidKey: document.body.dataset.vapidKey,
    mercureUrl: document.body.dataset.mercureUrl,
    mercureTopic: document.body.dataset.mercureTopic
};

// Btn accepter / terminer
window.forceAccept = function(btn) {
    let vId = btn.getAttribute("data-visite-id");
    if (vId === "__entity_id__" || !vId) {
        const row = btn.closest("tr");
        vId = row ? row.getAttribute("data-id") : null;
    }
    const eId = btn.getAttribute("data-etudiant-id");
    if (!vId) return alert("ID manquant");

    btn.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
    btn.classList.add("action-busy");

    fetch("/api/visite/accept", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ visiteId: parseInt(vId), etudiantId: parseInt(eId) })
    }).then(() => window.location.reload());
};

window.forceFinish = function(btn) {
    let vId = btn.getAttribute("data-visite-id");
    if (vId === "__entity_id__" || !vId) {
        const row = btn.closest("tr");
        vId = row ? row.getAttribute("data-id") : null;
    }
    if (!vId || !confirm("Terminer ?")) return;

    btn.innerHTML = '<i class="fa fa-spin fa-spinner"></i>';
    btn.classList.add("action-busy");

    fetch("/api/visite/finish", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ visiteId: parseInt(vId) })
    }).then(response => {
        if (response.ok) showReviewQRCode(vId);
        else { alert("Erreur"); window.location.reload(); }
    });
};

function showReviewQRCode(visiteId) {
    const reviewUrl = `http://localhost:3000/avis`;
    Swal.fire({
        title: "Visite terminée !",
        html: '<p>Faites scanner ce code au visiteur :</p><div id="qrcode-container" style="display:flex; justify-content:center; margin:20px 0;"></div>',
        icon: "success",
        didOpen: () => {
            new QRCode(document.getElementById("qrcode-container"), {
                text: reviewUrl, width: 180, height: 180
            });
        }
    }).then(() => window.location.reload());
}

// Push notif
if ("serviceWorker" in navigator) navigator.serviceWorker.register("/sw.js");

window.subscribeToNotifications = async function() {
    try {
        const reg = await navigator.serviceWorker.ready;
        const sub = await reg.pushManager.subscribe({
            userVisibleOnly: true,
            applicationServerKey: urlBase64ToUint8Array(config.vapidKey)
        });
        await fetch("/api/push-subscribe", {
            method: "POST",
            body: JSON.stringify(sub),
            headers: { "Content-Type": "application/json" }
        });
        alert("Alertes actives !");
    } catch (e) { console.error(e); }
};

function urlBase64ToUint8Array(base64String) {
    const padding = "=".repeat((4 - base64String.length % 4) % 4);
    const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
    const rawData = window.atob(base64);
    const outputArray = new Uint8Array(rawData.length);
    for (let i = 0; i < rawData.length; ++i) { outputArray[i] = rawData.charCodeAt(i); }
    return outputArray;
}

// Refresh auto avec mercure
if (config.mercureUrl) {
    const url = new URL(config.mercureUrl);
    url.searchParams.append("topic", config.mercureTopic);
    const eventSource = new EventSource(url);

    eventSource.onmessage = event => {
        if (document.querySelector(".action-busy") || document.querySelector(".swal2-container")) return;
        const data = JSON.parse(event.data);
        if (data.type === "NEW_VISITOR" && window.location.href.toLowerCase().includes("visite")) {
            window.location.reload();
        }
    };
}