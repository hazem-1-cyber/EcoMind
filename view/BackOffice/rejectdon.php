<?php
require_once __DIR__ . "/../../controller/DonController.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: lisdon.php?msg=invalid_id');
    exit;
}

$donCtrl = new DonController();
$id = (int)$_GET['id'];

// Vérifier que le don existe
$don = $donCtrl->getDon($id);
if (!$don) {
    header('Location: lisdon.php?msg=don_not_found');
    exit;
}

// Vérifier que ce n'est pas un don monétaire
if ($don['type_don'] === 'money') {
    header('Location: lisdon.php?msg=cannot_reject_money');
    exit;
}

// Vérifier que le don est en attente
if ($don['statut'] !== 'pending') {
    header('Location: lisdon.php?msg=don_not_pending');
    exit;
}

// Rejeter le don (l'envoyer à la corbeille)
$success = $donCtrl->rejectDon($id, 'Don matériel rejeté par l\'administrateur');

if ($success) {
    // Redirection avec message de succès
    header('Location: lisdon.php?msg=reject_success');
} else {
    // Redirection avec message d'erreur
    header('Location: lisdon.php?msg=reject_error');
}
exit;
?>