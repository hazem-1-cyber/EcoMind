<?php
/**
 * Configuration Email LOCALE pour tests (sans SMTP)
 * 
 * Cette configuration simule l'envoi d'emails en les enregistrant dans un fichier
 * Utile pour tester sans configurer Gmail
 */

// Mode local (pas d'envoi réel)
define('EMAIL_MODE', 'local'); // 'smtp' ou 'local'

// Configuration SMTP (pour mode 'smtp')
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_SECURE', 'tls');

// Identifiants SMTP
define('SMTP_USERNAME', 'ecomindp@gmail.com');
define('SMTP_PASSWORD', 'REMPLACER_PAR_MOT_DE_PASSE_APPLICATION');

// Expéditeur
define('EMAIL_FROM', 'noreply@ecomind.tn');
define('EMAIL_FROM_NAME', 'EcoMind');

// Options
define('EMAIL_DEBUG', false);

// Dossier pour sauvegarder les emails en mode local
define('EMAIL_LOG_DIR', __DIR__ . '/../logs/emails/');
?>
