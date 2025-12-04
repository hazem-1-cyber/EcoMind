<?php
require_once 'C:/xampp/htdocs/projet_web/config/config.php'; // chemin vers ta classe config

// Vérifier si ID existe dans l'URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request.");
}

$id = intval($_GET['id']); // sécurisation

try {
    $pdo = config::getConnection();

    // Vérifier si user existe
    $check = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $check->execute([$id]);
    $user = $check->fetch();

    if (!$user) {
        die("User not found.");
    }

    // Bannir le user
    $ban = $pdo->prepare("UPDATE users SET is_banned = 1 WHERE id = ?");
    $ban->execute([$id]);

    // Retour au dashboard
    header("Location: index.php?msg=banned");
    exit();

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
