<?php
/**
 * Script de test pour l'envoi d'emails
 * Accédez à ce fichier via : http://localhost/test_email.php
 */

require_once __DIR__ . '/helpers/EmailHelper.php';

echo "<h1>Test d'envoi d'email - EcoMind</h1>";
echo "<hr>";

// Test 1: Email de nouveau don à EcoMind
echo "<h2>Test 1: Notification nouveau don à EcoMind</h2>";
$donData = [
    'type_don' => 'money',
    'montant' => 150.00,
    'email' => 'donateur@example.com',
    'description' => 'Don monétaire de test'
];

$result1 = EmailHelper::notifyNewDonation($donData);
echo $result1 ? "✅ Email envoyé avec succès à EcoMind<br>" : "❌ Erreur d'envoi<br>";

echo "<hr>";

// Test 2: Email d'acceptation au donneur
echo "<h2>Test 2: Notification d'acceptation au donneur</h2>";
$donDataAccepted = [
    'type_don' => 'money',
    'montant' => 150.00,
    'email' => 'donateur@example.com',
    'created_at' => date('Y-m-d H:i:s'),
    'description' => 'Don monétaire de test'
];

$result2 = EmailHelper::notifyDonationAccepted($donDataAccepted);
echo $result2 ? "✅ Email d'acceptation envoyé au donneur<br>" : "❌ Erreur d'envoi<br>";

echo "<hr>";

// Test 3: Email de rejet au donneur
echo "<h2>Test 3: Notification de rejet au donneur</h2>";
$donDataRejected = [
    'type_don' => 'clothes',
    'montant' => null,
    'email' => 'donateur@example.com',
    'created_at' => date('Y-m-d H:i:s'),
    'description' => 'Vêtements usagés'
];

$result3 = EmailHelper::notifyDonationRejected($donDataRejected, 'Les vêtements ne correspondent pas à nos critères actuels');
echo $result3 ? "✅ Email de rejet envoyé au donneur<br>" : "❌ Erreur d'envoi<br>";

echo "<hr>";
echo "<p><strong>Note:</strong> Vérifiez votre boîte de réception et le dossier spam.</p>";
echo "<p><strong>Configuration:</strong> Les emails sont envoyés depuis " . EMAIL_FROM . " via " . SMTP_HOST . "</p>";
?>
