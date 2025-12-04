<?php
// api/notifications.php - OOP version
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Proposition.php';
require_once __DIR__ . '/../Service/DashboardService.php';

header('Content-Type: application/json');

try {
    $evenementModel = new Evenement();
    $inscriptionModel = new Inscription();
    $propositionModel = new Proposition();
    
    $dashboardService = new DashboardService(
        $evenementModel,
        $inscriptionModel,
        $propositionModel
    );
    
    $notifications = $dashboardService->getNotifications();
    
    echo json_encode([
        'success' => true,
        'inscriptionsToday' => $notifications['inscriptionsToday'],
        'totalPropositions' => $notifications['totalPropositions'],
        'totalNotifications' => $notifications['totalNotifications']
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
