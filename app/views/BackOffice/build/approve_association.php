<?php
session_start();
require_once 'C:/xampp/htdocs/projet_web/config/config.php';
require_once 'C:/xampp/htdocs/projet_web/app/controllers/FrontOfficeController.php';

// Vérifier que l'admin est connecté
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    die("Access denied: admin only");
}

// Vérifier que l'ID est présent
if (!isset($_GET['id'])) {
    die("Error: No user ID provided");
}

$userId = intval($_GET['id']);

$controller = new FrontOfficeController();
$user = $controller->getUserById($userId);

// Vérifier si l'utilisateur existe
if (!$user) {
    die("Error: User not found");
}

// Vérifier si la patente_image est fournie
if (empty($user['patente_image'])) {
    die("Error: This user did not upload a patente image");
}

// Mettre à jour le rôle en 'association'
$sql = "UPDATE users SET role = 'association' WHERE id = :id";
$stmt = Config::getConnection()->prepare($sql);
$stmt->execute(['id' => $userId]);

// Redirection
header("Location: index.php");
exit;
?>
