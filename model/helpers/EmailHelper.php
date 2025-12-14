<?php
/**
 * Helper pour l'envoi d'emails avec PHPMailer
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/email_config.php';

class EmailHelper {
    
    /**
     * Envoyer un email
     */
    public static function sendEmail($to, $toName, $subject, $body, $isHTML = true) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Debug
            if (EMAIL_DEBUG) {
                $mail->SMTPDebug = 2;
            }
            
            // Expéditeur
            $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
            
            // Destinataire
            $mail->addAddress($to, $toName);
            
            // Contenu
            $mail->isHTML($isHTML);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Envoyer
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur d'envoi d'email : " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Envoyer un email avec pièce jointe
     */
    public static function sendEmailWithAttachment($to, $toName, $subject, $body, $attachmentPath, $attachmentName = null) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Debug
            if (EMAIL_DEBUG) {
                $mail->SMTPDebug = 2;
            }
            
            // Expéditeur
            $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
            
            // Destinataire
            $mail->addAddress($to, $toName);
            
            // Pièce jointe
            if (file_exists($attachmentPath)) {
                $mail->addAttachment($attachmentPath, $attachmentName ?? basename($attachmentPath));
            }
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Envoyer
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur d'envoi d'email avec pièce jointe : " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Notifier EcoMind d'un nouveau don
     */
    public static function notifyNewDonation($donData) {
        $subject = "🌱 Nouveau don reçu - EcoMind";
        
        $body = self::getEmailTemplate([
            'title' => 'Nouveau don reçu !',
            'content' => "
                <p>Un nouveau don vient d'être enregistré sur la plateforme EcoMind.</p>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                    <h3 style='color: #2c5f2d; margin-top: 0;'>Détails du don</h3>
                    <p><strong>Type de don :</strong> " . ucfirst(str_replace('_', ' ', $donData['type_don'])) . "</p>
                    <p><strong>Email du donneur :</strong> " . htmlspecialchars($donData['email']) . "</p>
                    " . ($donData['type_don'] === 'money' ? 
                        "<p><strong>Montant :</strong> " . number_format($donData['montant'], 2) . " TND</p>" : 
                        "<p><strong>Description :</strong> " . htmlspecialchars($donData['description'] ?? 'N/A') . "</p>") . "
                    <p><strong>Date :</strong> " . date('d/m/Y à H:i') . "</p>
                </div>
                
                <p>Connectez-vous au BackOffice pour gérer ce don.</p>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='http://localhost/view/BackOffice/lisdon.php' 
                       style='background: linear-gradient(135deg, #2c5f2d, #88b04b); 
                              color: white; 
                              padding: 15px 30px; 
                              text-decoration: none; 
                              border-radius: 8px; 
                              display: inline-block;
                              font-weight: bold;'>
                        Gérer les dons
                    </a>
                </div>
            "
        ]);
        
        return self::sendEmail(SMTP_USERNAME, 'EcoMind Admin', $subject, $body);
    }
    
    /**
     * Envoyer email d'acceptation avec reçu en pièce jointe (UN SEUL EMAIL)
     */
    public static function sendAcceptanceWithReceipt($donData, $receiptPath) {
        $subject = "✅ Don accepté - Votre reçu fiscal - EcoMind";
        
        $body = self::getEmailTemplate([
            'title' => '🎉 Votre don a été accepté !',
            'content' => "
                <p style='font-size: 16px;'>Bonjour,</p>
                
                <div style='background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%); padding: 25px; border-radius: 15px; margin: 25px 0; border-left: 5px solid #28a745; box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);'>
                    <h3 style='color: #155724; margin-top: 0; font-size: 22px; display: flex; align-items: center; gap: 10px;'>
                        <span style='font-size: 32px;'>✅</span>
                        Don accepté avec succès !
                    </h3>
                    <p style='color: #155724; margin: 10px 0 0 0; font-size: 15px; line-height: 1.6;'>
                        Nous avons le plaisir de vous informer que votre don a été validé et accepté par notre équipe.
                    </p>
                </div>
                
                <div style='background: linear-gradient(135deg, #f0fff4 0%, #e8f5e9 100%); padding: 25px; border-radius: 15px; margin: 25px 0; border: 2px solid #A8E6CF;'>
                    <h3 style='color: #2c5f2d; margin-top: 0; font-size: 20px;'>📋 Détails de votre don</h3>
                    <table style='width: 100%; border-collapse: collapse;'>
                        <tr>
                            <td style='padding: 10px 0; color: #6c757d; font-weight: 600;'>Type de don :</td>
                            <td style='padding: 10px 0; color: #2c5f2d; font-weight: 700; text-align: right;'>" . ucfirst(str_replace('_', ' ', $donData['type_don'])) . "</td>
                        </tr>
                        " . ($donData['type_don'] === 'money' ? 
                            "<tr>
                                <td style='padding: 10px 0; color: #6c757d; font-weight: 600;'>Montant :</td>
                                <td style='padding: 10px 0; color: #ff9800; font-weight: 700; text-align: right; font-size: 20px;'>" . number_format($donData['montant'], 2) . " TND</td>
                            </tr>" : 
                            "<tr>
                                <td style='padding: 10px 0; color: #6c757d; font-weight: 600;'>Description :</td>
                                <td style='padding: 10px 0; color: #2c5f2d; font-weight: 600; text-align: right;'>" . htmlspecialchars($donData['description_don'] ?? 'Don matériel') . "</td>
                            </tr>") . "
                        <tr>
                            <td style='padding: 10px 0; color: #6c757d; font-weight: 600;'>Date :</td>
                            <td style='padding: 10px 0; color: #2c5f2d; font-weight: 600; text-align: right;'>" . date('d/m/Y', strtotime($donData['created_at'])) . "</td>
                        </tr>
                    </table>
                </div>
                
                <div style='background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); padding: 20px; border-radius: 12px; margin: 25px 0; border-left: 5px solid #ffc107;'>
                    <p style='margin: 0; color: #856404; font-size: 15px; line-height: 1.6;'>
                        <strong style='font-size: 18px;'>📎 Reçu fiscal joint</strong><br>
                        Votre reçu fiscal officiel est joint à cet email. Conservez-le précieusement pour votre déclaration d'impôts.
                    </p>
                </div>
                
                <div style='text-align: center; margin: 35px 0; padding: 30px; background: linear-gradient(135deg, #2c5f2d 0%, #88b04b 100%); border-radius: 15px; box-shadow: 0 8px 25px rgba(44, 95, 45, 0.3);'>
                    <h3 style='color: white; margin: 0 0 15px 0; font-size: 24px;'>💚 Merci pour votre générosité !</h3>
                    <p style='color: #A8E6CF; margin: 0; font-size: 16px; line-height: 1.6;'>
                        Grâce à vous, nous pouvons continuer notre mission de protection de l'environnement en Tunisie. 
                        Votre contribution fait une réelle différence pour notre planète. 🌍
                    </p>
                </div>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 12px; margin: 25px 0;'>
                    <h4 style='color: #2c5f2d; margin-top: 0; font-size: 18px;'>🌱 EcoMind - Association innovante</h4>
                    <p style='color: #6c757d; margin: 10px 0; font-size: 14px; line-height: 1.6;'>
                        EcoMind est une association tunisienne pionnière dans la protection de l'environnement. 
                        Nous utilisons des technologies innovantes et des approches futuristes pour créer un impact durable.
                    </p>
                    <p style='color: #6c757d; margin: 10px 0 0 0; font-size: 14px;'>
                        <strong>Notre vision :</strong> Un avenir écologique et durable pour la Tunisie 🇹🇳
                    </p>
                </div>
                
                <p style='color: #6c757d; font-size: 14px; margin-top: 30px; text-align: center;'>
                    Si vous avez des questions, n'hésitez pas à nous contacter.<br>
                    <strong>Email :</strong> contact@ecomind.tn | <strong>Tél :</strong> +216 XX XXX XXX
                </p>
            "
        ]);
        
        return self::sendEmailWithAttachment($donData['email'], 'Cher donateur', $subject, $body, $receiptPath, 'Recu_EcoMind_' . $donData['id'] . '.pdf');
    }
    
    /**
     * Notifier le donneur que son don est accepté (ANCIENNE VERSION - gardée pour compatibilité)
     */
    public static function notifyDonationAccepted($donData) {
        $subject = "✅ Votre don a été accepté - EcoMind";
        
        $body = self::getEmailTemplate([
            'title' => 'Don accepté !',
            'content' => "
                <p>Bonjour,</p>
                
                <p>Nous avons le plaisir de vous informer que votre don a été <strong style='color: #28a745;'>accepté</strong> ! 🎉</p>
                
                <div style='background: #d4edda; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #28a745;'>
                    <h3 style='color: #155724; margin-top: 0;'>Détails de votre don</h3>
                    <p><strong>Type de don :</strong> " . ucfirst(str_replace('_', ' ', $donData['type_don'])) . "</p>
                    " . ($donData['type_don'] === 'money' ? 
                        "<p><strong>Montant :</strong> " . number_format($donData['montant'], 2) . " TND</p>" : 
                        "<p><strong>Description :</strong> " . htmlspecialchars($donData['description'] ?? 'N/A') . "</p>") . "
                    <p><strong>Date :</strong> " . date('d/m/Y', strtotime($donData['created_at'])) . "</p>
                </div>
                
                <p>Votre générosité contribue directement à la protection de notre environnement. Merci de faire partie de la communauté EcoMind ! 🌱</p>
                
                <p style='color: #6c757d; font-size: 14px; margin-top: 30px;'>
                    Si vous avez des questions, n'hésitez pas à nous contacter.
                </p>
            "
        ]);
        
        return self::sendEmail($donData['email'], 'Cher donateur', $subject, $body);
    }
    
    /**
     * Notifier le donneur que son don est rejeté
     */
    public static function notifyDonationRejected($donData, $reason = '') {
        $subject = "❌ Votre don n'a pas pu être accepté - EcoMind";
        
        $body = self::getEmailTemplate([
            'title' => 'Don non accepté',
            'content' => "
                <p>Bonjour,</p>
                
                <p>Nous vous remercions pour votre générosité, mais malheureusement nous ne pouvons pas accepter votre don pour le moment.</p>
                
                <div style='background: #f8d7da; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #dc3545;'>
                    <h3 style='color: #721c24; margin-top: 0;'>Détails de votre don</h3>
                    <p><strong>Type de don :</strong> " . ucfirst(str_replace('_', ' ', $donData['type_don'])) . "</p>
                    " . ($donData['type_don'] === 'money' ? 
                        "<p><strong>Montant :</strong> " . number_format($donData['montant'], 2) . " TND</p>" : 
                        "<p><strong>Description :</strong> " . htmlspecialchars($donData['description'] ?? 'N/A') . "</p>") . "
                    " . ($reason ? "<p><strong>Raison :</strong> " . htmlspecialchars($reason) . "</p>" : "") . "
                </div>
                
                <p>N'hésitez pas à nous contacter si vous avez des questions ou si vous souhaitez faire un nouveau don.</p>
                
                <p style='color: #6c757d; font-size: 14px; margin-top: 30px;'>
                    Merci de votre compréhension et de votre soutien à notre cause environnementale.
                </p>
            "
        ]);
        
        return self::sendEmail($donData['email'], 'Cher donateur', $subject, $body);
    }
    
    /**
     * Template HTML pour les emails
     */
    private static function getEmailTemplate($data) {
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>" . $data['title'] . "</title>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>
                            <!-- Header -->
                            <tr>
                                <td style='background: linear-gradient(135deg, #2c5f2d, #88b04b); padding: 30px; text-align: center;'>
                                    <h1 style='color: white; margin: 0; font-size: 32px;'>
                                        🌱 EcoMind
                                    </h1>
                                    <p style='color: #A8E6CF; margin: 10px 0 0 0; font-size: 16px;'>
                                        Ensemble pour l'environnement
                                    </p>
                                </td>
                            </tr>
                            
                            <!-- Content -->
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='color: #2c5f2d; margin-top: 0;'>" . $data['title'] . "</h2>
                                    " . $data['content'] . "
                                </td>
                            </tr>
                            
                            <!-- Footer -->
                            <tr>
                                <td style='background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 2px solid #A8E6CF;'>
                                    <p style='color: #6c757d; margin: 0; font-size: 14px;'>
                                        © " . date('Y') . " EcoMind - Tous droits réservés
                                    </p>
                                    <p style='color: #6c757d; margin: 10px 0 0 0; font-size: 12px;'>
                                        Cet email a été envoyé automatiquement, merci de ne pas y répondre.
                                    </p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        ";
    }
    
    /**
     * Envoyer un email de remerciement au donneur après paiement
     */
    public static function sendThankYouEmail($donData, $receiptPath = null) {
        $subject = "🌱 Merci pour votre généreux don - EcoMind";
        
        $content = "
            <p style='font-size: 16px; color: #2c5f2d; line-height: 1.6;'>
                Cher(e) donateur/donatrice,
            </p>
            <p style='font-size: 16px; color: #4a5f4a; line-height: 1.6;'>
                Nous vous remercions chaleureusement pour votre don de <strong>" . number_format($donData['montant'], 2) . " TND</strong>.
            </p>
            <p style='font-size: 16px; color: #4a5f4a; line-height: 1.6;'>
                Votre générosité nous permet de continuer notre mission de protection de l'environnement en Tunisie.
                Chaque contribution compte et fait une réelle différence pour notre planète.
            </p>
            
            <div style='background: linear-gradient(135deg, #e8f5e9, #c8e6c9); padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #4CAF50;'>
                <h3 style='color: #2c5f2d; margin-top: 0;'>💚 Votre impact</h3>
                <p style='color: #4a5f4a; margin: 0;'>
                    Grâce à vous, nous pouvons planter des arbres, nettoyer nos plages et sensibiliser les jeunes générations à l'écologie.
                </p>
            </div>
            
            <p style='font-size: 16px; color: #4a5f4a; line-height: 1.6;'>
                Vous trouverez ci-joint votre reçu de don officiel.
            </p>
            
            <p style='font-size: 16px; color: #2c5f2d; line-height: 1.6; margin-top: 30px;'>
                Avec toute notre gratitude,<br>
                <strong>L'équipe EcoMind</strong> 🌍
            </p>
        ";
        
        $body = self::getEmailTemplate([
            'title' => 'Merci pour votre générosité !',
            'content' => $content
        ]);
        
        $mail = new PHPMailer(true);
        
        try {
            // Configuration SMTP
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_SECURE;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Expéditeur
            $mail->setFrom(EMAIL_FROM, EMAIL_FROM_NAME);
            
            // Destinataire
            $mail->addAddress($donData['email']);
            
            // Pièce jointe (reçu PDF)
            if ($receiptPath && file_exists($receiptPath)) {
                $mail->addAttachment($receiptPath, 'recu_don_ecomind.pdf');
            }
            
            // Contenu
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            // Envoyer
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur envoi email remerciement: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Envoyer une notification à l'admin après un don
     */
    public static function sendAdminNotification($donData, $adminEmail) {
        $subject = "🔔 Nouveau don reçu - EcoMind";
        
        $content = "
            <p style='font-size: 16px; color: #2c5f2d; line-height: 1.6;'>
                <strong>Un nouveau don vient d'être effectué !</strong>
            </p>
            
            <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0;'>
                <h3 style='color: #2c5f2d; margin-top: 0;'>📋 Détails du don</h3>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #6c757d;'><strong>Montant:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #2c5f2d;'><strong>" . number_format($donData['montant'], 2) . " TND</strong></td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #6c757d;'><strong>Email donneur:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #2c5f2d;'>" . htmlspecialchars($donData['email']) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #6c757d;'><strong>Type:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #2c5f2d;'>" . ucfirst($donData['type_don']) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #6c757d;'><strong>Date:</strong></td>
                        <td style='padding: 8px; border-bottom: 1px solid #dee2e6; color: #2c5f2d;'>" . date('d/m/Y H:i', strtotime($donData['created_at'])) . "</td>
                    </tr>
                    <tr>
                        <td style='padding: 8px; color: #6c757d;'><strong>Statut:</strong></td>
                        <td style='padding: 8px; color: #2c5f2d;'>" . ($donData['statut'] === 'validated' ? '✅ Validé' : '⏳ En attente') . "</td>
                    </tr>
                </table>
            </div>
            
            <p style='font-size: 16px; color: #4a5f4a; line-height: 1.6;'>
                Connectez-vous au back-office pour gérer ce don.
            </p>
            
            <div style='text-align: center; margin: 30px 0;'>
                <a href='http://localhost/ecomind/view/BackOffice/lisdon.php' style='display: inline-block; background: linear-gradient(135deg, #2c5f2d, #88b04b); color: white; padding: 15px 30px; border-radius: 50px; text-decoration: none; font-weight: 600;'>
                    Gérer les dons
                </a>
            </div>
        ";
        
        $body = self::getEmailTemplate([
            'title' => 'Nouveau don reçu',
            'content' => $content
        ]);
        
        return self::sendEmail($adminEmail, 'Admin EcoMind', $subject, $body);
    }

}
?>