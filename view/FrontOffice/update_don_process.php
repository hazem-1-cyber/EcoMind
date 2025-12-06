<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../model/DonModel.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Méthode non autorisée'
    ]);
    exit;
}

if (!isset($_POST['id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'ID du don manquant'
    ]);
    exit;
}

$id = (int)$_POST['id'];

// Vérifier que le don existe et est en attente
$donCtrl = new DonController();
$donExistant = $donCtrl->getDon($id);

if (!$donExistant || $donExistant['statut'] !== 'pending') {
    echo json_encode([
        'success' => false,
        'message' => 'Don non trouvé ou non modifiable'
    ]);
    exit;
}

// Créer l'objet Don avec les nouvelles valeurs
$don = new Don(
    $id,
    $donExistant['type_don'], // Type non modifiable
    isset($_POST['montant']) && !empty($_POST['montant']) ? (float)$_POST['montant'] : $donExistant['montant'],
    $donExistant['livraison'], // Livraison non modifiable
    $_POST['ville'] ?? $donExistant['ville'],
    $_POST['cp'] ?? $donExistant['cp'],
    $donExistant['adresse'], // Adresse non modifiable
    $donExistant['localisation'], // Localisation non modifiable
    $_POST['tel'] ?? $donExistant['tel'],
    $_POST['description_don'] ?? $donExistant['description_don'],
    $donExistant['association_id'], // Association non modifiable
    $donExistant['email'], // Email non modifiable
    $donExistant['statut'], // Statut non modifiable
    $donExistant['created_at']
);

try {
    $donCtrl->updateDon($don, $id);
    
    echo json_encode([
        'success' => true,
        'message' => 'Don modifié avec succès'
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Erreur lors de la modification: ' . $e->getMessage()
    ]);
}
?>
