<?php
session_start();
require_once '../../../config/config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=donation');
    exit();
}

$message = '';
$error = '';

// Trait