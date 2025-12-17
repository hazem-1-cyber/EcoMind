<?php
session_start();

// Vérifier que c'est un admin
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    http_response_code(403);
    echo json_encode(['error' => 'Accès non autorisé']);
    exit;
}

$base = dirname(__DIR__, 2);
require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/ia_conseil_generator.php';

// Vérifier que c'est une requête POST avec du JSON
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Méthode non autorisée']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Données JSON invalides']);
    exit;
}

try {
    // Créer une réponse de test avec les données reçues
    $reponseTest = new ReponseFormulaire(
        999, // ID de test
        'test@ajax.com',
        (int)($data['nb_personnes'] ?? 1),
        (int)($data['douche_freq'] ?? 7),
        (int)($data['douche_duree'] ?? 10),
        $data['chauffage'] ?? 'electrique',
        (int)($data['temp_hiver'] ?? 20),
        $data['transport'] ?? 'voiture',
        (int)($data['distance'] ?? 10)
    );
    
    // Générer les conseils avec l'IA
    $generator = new IAConseilGenerator();
    $conseils = $generator->genererConseils($reponseTest);
    
    // Retourner les conseils en JSON
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'eau' => $conseils['eau'],
        'energie' => $conseils['energie'],
        'transport' => $conseils['transport'],
        'mode' => 'IA personnalisée'
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la génération des conseils',
        'message' => $e->getMessage()
    ]);
}
?>