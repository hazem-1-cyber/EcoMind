<?php
session_start();
require_once 'C:/xampp/htdocs/projet_web/config/config.php';
require_once 'C:/xampp/htdocs/projet_web/app/controllers/FrontOfficeController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$controller = new FrontOfficeController();
$user_id = $_SESSION['user_id'];

// Vérifier si fichier envoyé
if (!isset($_FILES['patente']) || $_FILES['patente']['error'] !== UPLOAD_ERR_OK) {
    die("Error uploading file.");
}

// Création du dossier si non existant
$uploadDir = "uploads/patentes/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Nom unique du fichier
$filename = uniqid("patente_") . "_" . basename($_FILES['patente']['name']);
$filepath = $uploadDir . $filename;

// Déplacer le fichier
if (!move_uploaded_file($_FILES['patente']['tmp_name'], $filepath)) {
    die("Error storing file.");
}

// Mise à jour BD

$conn = config::getConnection();
$pdo = $conn;
$stmt = $pdo->prepare("
    UPDATE users 
    SET patente_image = :image
    WHERE id = :id
");

$stmt->execute([
    ':image' => $filename,
    ':id' => $user_id
]);

echo "Patente image uploaded successfully!";
echo "<br><a href='index.php'>Return homepage</a>";
?>
