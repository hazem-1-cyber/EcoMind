<?php
require_once __DIR__ . '/../../controller/categorieController.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: listcategorie.php');
    exit;
}

$id = (int)$_GET['id'];
$categorieCtrl = new CategorieController();

if ($categorieCtrl->deleteCategorie($id)) {
    header('Location: listcategorie.php?success=deleted');
} else {
    header('Location: listcategorie.php?error=delete_failed');
}
exit;
?>
