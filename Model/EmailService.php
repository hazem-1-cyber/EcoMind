<?php
// Model/EmailService.php - Service d'email avec PHPMailer et Gmail

require_once __DIR__ . '/../vendor/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    private $fromEmail = 'lexihalers@gmail.com';
    private $fromName = 'EcoMind';
    private $gmailPassword = 'hysz xjxp emfe elro';
    
    public function __construct() {
        // Configuration Gmail directement dans le code
    }
    
    /**
     * Envoyer un email de bienvenue et confirmation d'inscription
     */
    public function sendWelcomeConfirmation($participantEmail, $participantName, $eventTitle) {
        // En mode développement, simuler l'envoi
        if (defined('DEVELOPMENT_MODE') && DEVELOPMENT_MODE) {
            error_log("📧 EMAIL SIMULÉ - À: $participantEmail, Participant: $participantName, Événement: $eventTitle");
            return true;
        }
        
        // Préparer l'email
        $subject = "Bienvenue chez EcoMind - Inscription confirmée !";
        $message = $this->createEmailMessage($participantName, $eventTitle, $participantEmail);
        
        // Envoyer avec PHPMailer et Gmail
        return $this->sendWithPHPMailer($participantEmail, $subject, $message);
    }
    
    /**
     * Envoyer avec PHPMailer et Gmail SMTP
     */
    private function sendWithPHPMailer($to, $subject, $message) {
        try {
            $mail = new PHPMailer(true);
            
            // Configuration serveur SMTP Gmail
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = $this->fromEmail;
            $mail->Password = $this->gmailPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Destinataires
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($to);
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->CharSet = 'UTF-8';
            
            // Envoyer
            $mail->send();
            
            error_log("✅ EMAIL ENVOYÉ via PHPMailer Gmail à: $to");
            return true;
            
        } catch (Exception $e) {
            error_log("❌ Erreur PHPMailer: " . $e->getMessage());
            return false;
        }
    }
    

    
    /**
     * Créer le message HTML de l'email
     */
    private function createEmailMessage($participantName, $eventTitle, $participantEmail) {
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #013220, #025a3a); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .highlight { background: #A8E6CF; padding: 15px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
                .logo { font-size: 24px; font-weight: bold; }
                h2, h3, h4 { margin-top: 0; }
                ul { padding-left: 20px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <div class='logo'>🌱 EcoMind</div>
                    <h2>Bienvenue dans notre communauté !</h2>
                </div>
                
                <div class='content'>
                    <h3>Bonjour " . htmlspecialchars($participantName) . " !</h3>
                    
                    <p>Nous sommes ravis de vous accueillir dans la communauté EcoMind ! 🎉</p>
                    
                    <div class='highlight'>
                        <h4>✅ Votre inscription est confirmée</h4>
                        <p><strong>Événement :</strong> " . htmlspecialchars($eventTitle) . "</p>
                        <p><strong>Email :</strong> " . htmlspecialchars($participantEmail) . "</p>
                    </div>
                    
                    <h4>Que se passe-t-il maintenant ?</h4>
                    <ul>
                        <li>Votre place est réservée pour cet événement</li>
                        <li>Vous recevrez une notification quand l'événement approchera</li>
                        <li>En cas de changement, nous vous tiendrons informé(e)</li>
                    </ul>
                    
                    <p>Merci de rejoindre notre mission pour un avenir plus durable ! 🌍</p>
                    
                    <p>À très bientôt,<br><strong>L'équipe EcoMind</strong></p>
                </div>
                
                <div class='footer'>
                    <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
                    <p>EcoMind - Ensemble pour l'environnement</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    

}