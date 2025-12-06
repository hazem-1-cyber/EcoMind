<?php
require_once __DIR__ . "/../../controller/DonController.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: lisdon.php');
    exit;
}

$donCtrl = new DonController();
$id = (int)$_GET['id'];

// Rejeter le don
$success = $donCtrl->rejectDon($id);

if ($success) {
    // Redirection avec message de succès
    header('Location: lisdon.php?msg=reject_success');
} else {
    // Redirection avec message d'erreur
    header('Location: lisdon.php?msg=reject_error');
}
exit;
?>