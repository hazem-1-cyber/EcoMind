<?php
session_start();
require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../model/DonModel.php";
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/vendor/autoload.php';
require_once __DIR__ . '/../../controller/config/SettingsManager.php';

$settingsManager = new SettingsManager();

// Vérifier que les données du don sont en session
if (!isset($_SESSION['don_data'])) {
    header('Location: don.html');
    exit;
}

// Vérifier que le payment_intent est présent
if (!isset($_GET['payment_intent'])) {
    $_SESSION['payment_errors'] = ['Paiement invalide'];
    header('Location: paiement.php');
    exit;
}

$donData = $_SESSION['don_data'];
$paymentIntentId = $_GET['payment_intent'];

try {
    // Initialiser Stripe
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Récupérer le Payment Intent pour vérifier le statut
    $paymentIntent = \Stripe\PaymentIntent::retrieve($paymentIntentId);

    // Vérifier que le paiement a réussi
    if ($paymentIntent->status !== 'succeeded') {
        $_SESSION['payment_errors'] = ['Le paiement n\'a pas été complété'];
        header('Location: paiement.php');
        exit;
    }

    // Vérifier que le montant correspond
    // Note: Le montant Stripe est en USD, le montant du don est en TND
    $montantStripeUSD = $paymentIntent->amount / 100; // Stripe utilise les centimes
    $montantDonTND = floatval($donData['montant']);
    $montantDonUSD = $montantDonTND / 3.1; // Conversion TND vers USD
    
    // Vérifier avec une tolérance de 1% pour les arrondis
    $tolerance = $montantDonUSD * 0.01;
    if (abs($montantStripeUSD - $montantDonUSD) > $tolerance) {
        error_log("Montant Stripe: $montantStripeUSD USD, Montant Don: $montantDonUSD USD (original: $montantDonTND TND)");
        $_SESSION['payment_errors'] = ['Le montant du paiement ne correspond pas'];
        header('Location: paiement.php');
        exit;
    }

    // Créer l'objet Don avec toutes les données
    $don = new Don();
    $don->setTypeDon($donData['type_don']);
    $don->setEmail(strtolower($donData['email']));
    $don->setAssociationId($donData['association_id']);
    
    // Vérifier si validation automatique activée
    $autoValidate = $settingsManager->isAutoValidateEnabled();
    $don->setStatut($autoValidate ? 'validated' : 'pending');
    
    // Utiliser le montant original en TND (pas le montant USD de Stripe)
    $don->setMontant($montantDonTND);
    $don->setLivraison('en_ligne'); // Paiement en ligne
    
    // Ajouter l'image si présente
    if (isset($donData['image_don'])) {
        $don->setImageDon($donData['image_don']);
    }

    // Enregistrer le don dans la base de données
    $donCtrl = new DonController();
    $donCtrl->addDon($don);
    
    // Récupérer l'ID du don qui vient d'être créé
    $db = Config::getConnexion();
    $lastDonId = $db->lastInsertId();
    
    // Envoyer les emails si les notifications sont activées
    $emailNotificationsEnabled = $settingsManager->get('email_notifications', true);
    error_log("Email notifications enabled: " . ($emailNotificationsEnabled ? 'true' : 'false'));
    
    if ($emailNotificationsEnabled) {
        require_once __DIR__ . '/../../controller/helpers/EmailHelper.php';
        require_once __DIR__ . '/../../controller/helpers/ReceiptHelper.php';
        
        // Récupérer les données complètes du don
        $donComplet = $donCtrl->getDon($lastDonId);
        error_log("Don récupéré pour email - ID: $lastDonId, Email: " . ($donComplet ? $donComplet['email'] : 'null'));
        
        if ($donComplet) {
            // 1. Email de remerciement au donneur avec reçu
            try {
                error_log("Tentative génération reçu PDF pour don ID: $lastDonId");
                
                // Générer le reçu PDF
                $receipt = ReceiptHelper::generateReceipt($donComplet);
                error_log("Reçu PDF généré: " . $receipt['filepath']);
                
                // Envoyer l'email de remerciement avec le reçu en pièce jointe
                error_log("Tentative envoi email remerciement à: " . $donComplet['email']);
                $emailResult = EmailHelper::sendThankYouEmail($donComplet, $receipt['filepath']);
                
                if ($emailResult) {
                    error_log("✅ Email de remerciement envoyé avec succès à: " . $donComplet['email']);
                } else {
                    error_log("❌ Échec envoi email de remerciement à: " . $donComplet['email']);
                }
                
            } catch (Exception $e) {
                error_log("❌ Erreur envoi email donneur: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());
            }
            
            // 2. Email de notification à l'admin EcoMind
            try {
                $adminEmail = $settingsManager->get('admin_email', 'contact@ecomind.tn');
                error_log("Tentative envoi notification admin à: $adminEmail");
                
                $adminResult = EmailHelper::sendAdminNotification($donComplet, $adminEmail);
                
                if ($adminResult) {
                    error_log("✅ Email admin envoyé avec succès à: $adminEmail");
                } else {
                    error_log("❌ Échec envoi email admin à: $adminEmail");
                }
                
            } catch (Exception $e) {
                error_log("❌ Erreur envoi email admin: " . $e->getMessage());
            }
        } else {
            error_log("❌ Impossible de récupérer les données du don ID: $lastDonId");
        }
    } else {
        error_log("📧 Notifications email désactivées dans les paramètres");
    }

    // Conserver l'association_id pour la page merci avant de nettoyer
    $_SESSION['merci_association_id'] = $donData['association_id'];
    $_SESSION['payment_intent_id'] = $paymentIntentId;

    // Nettoyer la session
    unset($_SESSION['don_data']);
    unset($_SESSION['payment_errors']);

    // Rediriger vers la page de remerciement
    header('Location: merci.php?payment=success');
    exit;

} catch (\Stripe\Exception\ApiErrorException $e) {
    $_SESSION['payment_errors'] = ['Erreur Stripe: ' . $e->getMessage()];
    header('Location: paiement.php');
    exit;
} catch (Exception $e) {
    $_SESSION['payment_errors'] = ['Erreur: ' . $e->getMessage()];
    header('Location: paiement.php');
    exit;
}
?>