<?php
// api/notifications.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Model/Database.php';

header('Content-Type: application/json');

try {
    $pdo = Database::getPdo();
    
    // Compter les inscriptions d'aujourd'hui
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM inscription WHERE DATE(date_inscription) = ?");
    $stmt->execute([$today]);
    $inscriptionsToday = (int) $stmt->fetch()['total'];
    
    // Compter les propositions
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM proposition");
    $totalPropositions = (int) $stmt->fetch()['total'];
    
    echo json_encode([
        'success' => true,
        'inscriptionsToday' => $inscriptionsToday,
        'totalPropositions' => $totalPropositions,
        'totalNotifications' => $inscriptionsToday + ($totalPropositions > 0 ? 1 : 0)
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
