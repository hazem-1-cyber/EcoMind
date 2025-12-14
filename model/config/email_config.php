<?php
/**
 * Configuration Email pour EcoMind
 * 
 * IMPORTANT : Pour utiliser Gmail, vous devez :
 * 1. Activer la validation en 2 étapes sur votre compte Google
 * 2. Générer un "Mot de passe d'application" :
 *    - Allez sur https://myaccount.google.com/security
 *    - Cliquez sur "Validation en 2 étapes"
 *    - En bas, cliquez sur "Mots de passe des applications"
 *    - Sélectionnez "Autre" et nommez-le "EcoMind"
 *    - Utilisez le mot de passe généré ci-dessous
 */

// Configuration SMTP
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls'); // 'tls' ou 'ssl'

// Identifiants SMTP
define('SMTP_USERNAME', 'ecomindp@gmail.com'); // Votre email Gmail
define('SMTP_PASSWORD', 'dvfa atuu guuy wqxl'); // Mot de passe d'application Gmail

// Expéditeur
define('EMAIL_FROM', 'noreply@ecomind.tn');
define('EMAIL_FROM_NAME', 'EcoMind');

// Options
define('EMAIL_DEBUG', false); // Mode production // Mettre à true pour déboguer
?>