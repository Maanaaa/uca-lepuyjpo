<?php
use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

$auth = [
    'VAPID' => [
        'subject' => 'mailto:ton-email@exemple.com',
        'publicKey' => $_ENV['VAPID_PUBLIC_KEY'],
        'privateKey' => $_ENV['VAPID_PRIVATE_KEY'],
    ],
];

$webPush = new WebPush($auth);

$subscription = Subscription::create([
    'endpoint' => 'L_URL_RECUPEREE_EN_BASE',
    'publicKey' => 'LA_CLE_P256DH_EN_BASE',
    'authToken' => 'LA_CLE_AUTH_EN_BASE',
]);

$report = $webPush->sendOneNotification(
    $subscription,
    json_encode(['title' => 'Test', 'body' => 'Ça marche !'])
);

if ($report->isSuccess()) {
    echo "L'envoi a réussi !";
} else {
    echo "Erreur : " . $report->getReason();
}

