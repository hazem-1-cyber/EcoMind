<?php
/**
 * Helper pour générer des reçus PDF
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../../config.php';

use Dompdf\Dompdf;
use Dompdf\Options;

class ReceiptHelper {
    
    /**
     * Générer un reçu PDF pour un don
     */
    public static function generateReceipt($donData) {
        // Configuration Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        
        // Générer le HTML du reçu
        $html = self::getReceiptHTML($donData);
        
        // Charger le HTML
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Créer le dossier uploads/receipts s'il n'existe pas
        $uploadDir = __DIR__ . '/../../view/FrontOffice/images/uploads/receipts/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Nom du fichier
        $filename = 'recu_' . $donData['id'] . '_' . time() . '.pdf';
        $filepath = $uploadDir . $filename;
        
        // Sauvegarder le PDF
        file_put_contents($filepath, $dompdf->output());
        
        return [
            'filename' => $filename,
            'filepath' => $filepath,
            'url' => 'view/FrontOffice/images/uploads/receipts/' . $filename
        ];
    }
    
    /**
     * Envoyer le reçu par email au donneur
     */
    public static function sendReceiptByEmail($donData, $receiptPath) {
        require_once __DIR__ . '/EmailHelper.php';
        
        $subject = "🧾 Votre reçu de don - EcoMind";
        
        $body = self::getReceiptEmailTemplate([
            'donData' => $donData,
            'receiptUrl' => 'http://localhost/' . str_replace('../', '', $receiptPath)
        ]);
        
        // Envoyer l'email avec pièce jointe
        return EmailHelper::sendEmailWithAttachment(
            $donData['email'],
            'Cher donateur',
            $subject,
            $body,
            $receiptPath
        );
    }
    
    /**
     * Template HTML pour le reçu PDF
     */
    private static function getReceiptHTML($donData) {
        $numeroRecu = str_pad($donData['id'], 6, '0', STR_PAD_LEFT);
        $dateRecu = date('d/m/Y', strtotime($donData['created_at']));
        $annee = date('Y', strtotime($donData['created_at']));
        
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <style>
                @page { margin: 20px; }
                body {
                    font-family: 'DejaVu Sans', Arial, sans-serif;
                    color: #333;
                    line-height: 1.6;
                }
                .header {
                    background: linear-gradient(135deg, #2c5f2d, #88b04b);
                    color: white;
                    padding: 30px;
                    text-align: center;
                    border-radius: 10px;
                    margin-bottom: 30px;
                }
                .header h1 {
                    margin: 0;
                    font-size: 36px;
                }
                .header p {
                    margin: 10px 0 0 0;
                    font-size: 18px;
                    opacity: 0.9;
                }
                .recu-info {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    margin-bottom: 30px;
                    border-left: 5px solid #A8E6CF;
                }
                .recu-info h2 {
                    color: #2c5f2d;
                    margin-top: 0;
                    font-size: 24px;
                }
                .info-row {
                    display: flex;
                    justify-content: space-between;
                    padding: 10px 0;
                    border-bottom: 1px solid #dee2e6;
                }
                .info-label {
                    font-weight: bold;
                    color: #2c5f2d;
                }
                .info-value {
                    color: #495057;
                }
                .montant-box {
                    background: #d4edda;
                    border: 3px solid #28a745;
                    padding: 25px;
                    text-align: center;
                    border-radius: 10px;
                    margin: 30px 0;
                }
                .montant-box h3 {
                    margin: 0 0 10px 0;
                    color: #155724;
                    font-size: 20px;
                }
                .montant-box .montant {
                    font-size: 48px;
                    font-weight: bold;
                    color: #155724;
                    margin: 10px 0;
                }
                .footer {
                    margin-top: 50px;
                    padding-top: 20px;
                    border-top: 2px solid #A8E6CF;
                    text-align: center;
                    color: #6c757d;
                    font-size: 12px;
                }
                .signature {
                    margin-top: 40px;
                    text-align: right;
                }
                .signature p {
                    margin: 5px 0;
                }
                .watermark {
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%) rotate(-45deg);
                    font-size: 100px;
                    color: rgba(168, 230, 207, 0.1);
                    z-index: -1;
                }
            </style>
        </head>
        <body>
            <div class='watermark'>ECOMIND</div>
            
            <div class='header'>
                <h1>🌱 EcoMind</h1>
                <p>Reçu de don fiscal</p>
            </div>
            
            <div class='recu-info'>
                <h2>Reçu N° " . $numeroRecu . "/" . $annee . "</h2>
                <div class='info-row'>
                    <span class='info-label'>Date d'émission :</span>
                    <span class='info-value'>" . $dateRecu . "</span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>Donateur :</span>
                    <span class='info-value'>" . htmlspecialchars($donData['email']) . "</span>
                </div>
                <div class='info-row'>
                    <span class='info-label'>Type de don :</span>
                    <span class='info-value'>" . ucfirst(str_replace('_', ' ', $donData['type_don'])) . "</span>
                </div>
            </div>
            
            " . ($donData['type_don'] === 'money' ? "
            <div class='montant-box'>
                <h3>Montant du don</h3>
                <div class='montant'>" . number_format($donData['montant'], 2) . " TND</div>
                <p style='margin: 10px 0 0 0; color: #155724;'>
                    En lettres : " . self::numberToWords($donData['montant']) . " dinars
                </p>
            </div>
            " : "
            <div style='background: #e7f3ff; border: 2px solid #0066cc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <h3 style='color: #004085; margin-top: 0;'>Description du don</h3>
                <p style='color: #004085; margin: 0;'>" . htmlspecialchars($donData['description_don'] ?? 'Don matériel') . "</p>
            </div>
            ") . "
            
            <div style='background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; margin: 30px 0;'>
                <p style='margin: 0; color: #856404;'>
                    <strong>Note fiscale :</strong> Ce reçu vous permet de bénéficier d'une déduction fiscale 
                    conformément à la législation tunisienne en vigueur. Conservez-le précieusement.
                </p>
            </div>
            
            <div class='signature'>
                <p><strong>Pour EcoMind</strong></p>
                <p style='margin-top: 40px;'>_____________________</p>
                <p>Signature et cachet</p>
            </div>
            
            <div class='footer'>
                <p><strong>EcoMind</strong> - Association de protection de l'environnement</p>
                <p>Email: contact@ecomind.tn | Tél: +216 XX XXX XXX</p>
                <p>Ce document a été généré automatiquement le " . date('d/m/Y à H:i') . "</p>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Template email pour envoyer le reçu
     */
    private static function getReceiptEmailTemplate($data) {
        $donData = $data['donData'];
        
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        </head>
        <body style='margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;'>
            <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
                <tr>
                    <td align='center'>
                        <table width='600' cellpadding='0' cellspacing='0' style='background-color: white; border-radius: 10px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.1);'>
                            <tr>
                                <td style='background: linear-gradient(135deg, #2c5f2d, #88b04b); padding: 30px; text-align: center;'>
                                    <h1 style='color: white; margin: 0; font-size: 32px;'>🌱 EcoMind</h1>
                                    <p style='color: #A8E6CF; margin: 10px 0 0 0; font-size: 16px;'>Votre reçu de don</p>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style='padding: 40px 30px;'>
                                    <h2 style='color: #2c5f2d; margin-top: 0;'>Merci pour votre générosité ! 🎉</h2>
                                    
                                    <p>Bonjour,</p>
                                    
                                    <p>Nous vous remercions chaleureusement pour votre don. Votre reçu fiscal est joint à cet email.</p>
                                    
                                    <div style='background: #f8f9fa; padding: 20px; border-radius: 10px; margin: 20px 0; border-left: 4px solid #A8E6CF;'>
                                        <h3 style='color: #2c5f2d; margin-top: 0;'>Détails de votre don</h3>
                                        <p><strong>Type :</strong> " . ucfirst(str_replace('_', ' ', $donData['type_don'])) . "</p>
                                        " . ($donData['type_don'] === 'money' ? 
                                            "<p><strong>Montant :</strong> " . number_format($donData['montant'], 2) . " TND</p>" : 
                                            "<p><strong>Description :</strong> " . htmlspecialchars($donData['description_don'] ?? 'Don matériel') . "</p>") . "
                                        <p><strong>Date :</strong> " . date('d/m/Y', strtotime($donData['created_at'])) . "</p>
                                    </div>
                                    
                                    <div style='background: #fff3cd; padding: 15px; border-radius: 8px; margin: 20px 0;'>
                                        <p style='margin: 0; color: #856404;'>
                                            <strong>💡 Important :</strong> Conservez ce reçu pour votre déclaration fiscale. 
                                            Il vous permettra de bénéficier d'une déduction d'impôts.
                                        </p>
                                    </div>
                                    
                                    <p>Grâce à vous, nous pouvons continuer notre mission de protection de l'environnement en Tunisie. 🌍</p>
                                    
                                    <div style='text-align: center; margin: 30px 0;'>
                                        <a href='" . $data['receiptUrl'] . "' 
                                           style='background: linear-gradient(135deg, #2c5f2d, #88b04b); 
                                                  color: white; 
                                                  padding: 15px 30px; 
                                                  text-decoration: none; 
                                                  border-radius: 8px; 
                                                  display: inline-block;
                                                  font-weight: bold;'>
                                            📥 Télécharger le reçu
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            
                            <tr>
                                <td style='background: #f8f9fa; padding: 20px 30px; text-align: center; border-top: 2px solid #A8E6CF;'>
                                    <p style='color: #6c757d; margin: 0; font-size: 14px;'>
                                        © " . date('Y') . " EcoMind - Tous droits réservés
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
     * Convertir un nombre en lettres (français)
     */
    private static function numberToWords($number) {
        $number = (int) $number;
        
        $units = ['', 'un', 'deux', 'trois', 'quatre', 'cinq', 'six', 'sept', 'huit', 'neuf'];
        $teens = ['dix', 'onze', 'douze', 'treize', 'quatorze', 'quinze', 'seize', 'dix-sept', 'dix-huit', 'dix-neuf'];
        $tens = ['', '', 'vingt', 'trente', 'quarante', 'cinquante', 'soixante', 'soixante-dix', 'quatre-vingt', 'quatre-vingt-dix'];
        
        if ($number == 0) return 'zéro';
        if ($number < 10) return $units[$number];
        if ($number < 20) return $teens[$number - 10];
        if ($number < 100) {
            $ten = (int)($number / 10);
            $unit = $number % 10;
            return $tens[$ten] . ($unit ? '-' . $units[$unit] : '');
        }
        if ($number < 1000) {
            $hundred = (int)($number / 100);
            $rest = $number % 100;
            $result = ($hundred > 1 ? $units[$hundred] . ' ' : '') . 'cent';
            if ($rest) $result .= ' ' . self::numberToWords($rest);
            return $result;
        }
        
        return number_format($number, 0, ',', ' ');
    }
}
?>