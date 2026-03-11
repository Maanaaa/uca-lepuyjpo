self.addEventListener('push', function(event) {
    const data = event.data.json();
    
    const options = {
        body: data.body,
        icon: '/logo_uca.webp',
        badge: '/favicon.ico',
        vibrate: [200, 100, 200],
        data: {
            visiteId: data.visiteId,
            etudiantId: data.etudiantId
        },
        actions: [
            { action: 'accept', title: '✅ Accepter la visite' },
            { action: 'close', title: 'Fermer' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    if (event.action === 'accept') {
        const vId = event.notification.data.visiteId;
        const eId = event.notification.data.etudiantId;

        // Appel direct à l'API d'acceptation
        event.waitUntil(
            fetch('/api/visite/accept', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ visiteId: vId, etudiantId: eId })
            }).then(() => {

                return clients.openWindow('/admin');
            })
        );
    }
});