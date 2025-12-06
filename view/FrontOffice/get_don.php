<?php
header('Content-Type: application/json');

require_once __DIR__ . "/../../controller/DonController.php";

if (!isset($_GET['id']) || !isset($_GET['email'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Paramètres manquants'
    ]);
    exit;
}

$id = (int)$_GET['id'];
$email = trim($_GET['email']);

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email invalide'
    ]);
    exit;
}

$donCtrl = new DonController();
$don = $donCtrl->getDon($id);

if ($don && $don['email'] === $email && $don['statut'] === 'pending') {
    echo json_encode([
        'success' => true,
        'don' => $don
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Don non trouvé ou non modifiable'
    ]);
}
?>
