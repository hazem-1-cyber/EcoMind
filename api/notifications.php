<?php
// api/notifications.php
header('Content-Type: application/json');

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Proposition.php';

$inscriptionModel = new Inscription();
$propositionModel = new Proposition();

$inscriptions = $inscriptionModel->getAll();
$propositions = $propositionModel->getAll();

// Count today's inscriptions
$today = date('Y-m-d');
$inscriptionsToday = 0;
foreach ($inscriptions as $ins) {
    if (strpos($ins['date_inscription'], $today) === 0) {
        $inscriptionsToday++;
    }
}

$totalNotifications = $inscriptionsToday + count($propositions);

echo json_encode([
    'success' => true,
    'inscriptionsToday' => $inscriptionsToday,
    'totalPropositions' => count($propositions),
    'totalNotifications' => $totalNotifications
]);
