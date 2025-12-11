<?php
// test_phpmailer_gmail.php - Test rapide du système d'email PHPMailer
require_once 'Model/EmailService.php';

echo "<h2>🧪 Test PHPMailer Gmail</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $testEmail = $_POST['email'] ?? '';
    
    if (!empty($testEmail) && filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
        echo "<h3>📧 Test d'envoi à : " . htmlspecialchars($testEmail) . "</h3>";
        
        try {
            $emailService = new EmailService();
            $result = $emailService->sendWelcomeConfirmation(
                $testEmail,
                'Test Utilisateur',
                'Événement de Test EcoMind'
            );
            
            if ($result) {
                echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<h3>🎉 EMAIL ENVOYÉ AVEC SUCCÈS !</h3>";
                echo "<p>Vérifiez votre boîte de réception (et le dossier spam).</p>";
                echo "<p><strong>Le système d'email EcoMind fonctionne parfaitement !</strong></p>";
                echo "</div>";
            } else {
                echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
                echo "<h3>❌ Erreur d'envoi</h3>";
                echo "<p>L'email n'a pas pu être envoyé. Vérifiez les logs d'erreur.</p>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>❌ Erreur</h3>";
            echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h3>⚠️ Email invalide</h3>";
        echo "<p>Veuillez saisir une adresse email valide.</p>";
        echo "</div>";
    }
}
?>

<style>
body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
input[type="email"] { width: 300px; padding: 10px; border: 2px solid #28a745; border-radius: 5px; }
button { background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
</style>

<form method="POST">
    <h3>🧪 Tester l'envoi d'email</h3>
    <p>Entrez votre email pour recevoir un email de test :</p>
    <label>Votre email :</label><br>
    <input type="email" name="email" required placeholder="votre@email.com">
    <button type="submit">📧 Envoyer Test</button>
</form>

<hr>
<h3>📋 Configuration actuelle</h3>
<ul>
    <li>✅ PHPMailer installé</li>
    <li>✅ Gmail SMTP configuré (lexihalers@gmail.com)</li>
    <li>✅ Mode développement : <?php echo (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) ? 'ON (emails simulés)' : 'OFF (vrais emails)'; ?></li>
</ul>

<p><a href="index.php">🏠 Retour à l'accueil</a></p>