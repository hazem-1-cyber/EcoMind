<?php
require_once __DIR__ . "/../../controller/DonController.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: lisdon.php');
    exit;
}

$donCtrl = new DonController();
$id = (int)$_GET['id'];

// Accepter le don
$success = $donCtrl->acceptDon($id);

if ($success) {
    // Redirection avec message de succès
    header('Location: lisdon.php?msg=accept_success');
} else {
    // Redirection avec message d'erreur
    header('Location: lisdon.php?msg=accept_error');
}
exit;
?>