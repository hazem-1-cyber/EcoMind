<?php
session_start();
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../model/vendor/autoload.php';

header('Content-Type: application/json');

// Vérifier que les données du don sont en session
if (!isset($_SESSION['don_data'])) {
    echo json_encode(['error' => 'Session expirée']);
    exit;
}

$donData = $_SESSION['don_data'];
$montant = floatval($donData['montant']);

if ($montant < 10) {
    echo json_encode(['error' => 'Montant invalide']);
    exit;
}

try {
    // Initialiser Stripe
    \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);

    // Calculer les frais d'application (optionnel - ex: 5% de commission)
    // Décommentez si vous voulez prendre une commission
    // $applicationFee = intval($montant * 100 * 0.05); // 5% de commission

    // Créer le Payment Intent
    // Note: Stripe ne supporte pas TND directement
    // On utilise USD comme devise de paiement
    // Le montant TND est converti en USD (taux approximatif: 1 USD ≈ 3.1 TND)
    $montantUSD = $montant / 3.1; // Conversion approximative TND vers USD
    
    $paymentIntentData = [
        'amount' => intval($montantUSD * 100), // Stripe utilise les centimes
        'currency' => 'usd', // USD au lieu de TND car Stripe ne supporte pas TND
        'description' => 'Don pour association - ' . $donData['email'] . ' (Montant original: ' . $montant . ' TND)',
        'receipt_email' => $donData['email'], // Envoyer un reçu par email
        'metadata' => [
            'email' => $donData['email'],
            'association_id' => $donData['association_id'],
            'type_don' => $donData['type_don'],
            'montant_tnd' => $montant, // Conserver le montant original en TND
            'montant_usd' => $montantUSD
        ],
        // L'argent sera transféré sur votre compte Stripe principal
        // Aucune configuration supplémentaire nécessaire pour recevoir l'argent
    ];

    // Si vous utilisez Stripe Connect pour transférer à différentes associations
    // Décommentez et configurez ceci:
    /*
    if (defined('STRIPE_CONNECT_ENABLED') && STRIPE_CONNECT_ENABLED) {
        // Récupérer l'ID du compte Stripe Connect de l'association
        $associationStripeAccount = getAssociationStripeAccount($donData['association_id']);
        
        if ($associationStripeAccount) {
            $paymentIntentData['transfer_data'] = [
                'destination' => $associationStripeAccount,
            ];
            // Optionnel: Prendre une commission
            // $paymentIntentData['application_fee_amount'] = $applicationFee;
        }
    }
    */

    $paymentIntent = \Stripe\PaymentIntent::create($paymentIntentData);

    echo json_encode([
        'clientSecret' => $paymentIntent->client_secret
    ]);

} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['error' => $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['error' => 'Erreur lors de la création du paiement']);
}

/**
 * Fonction pour récupérer le compte Stripe Connect d'une association
 * À implémenter si vous utilisez Stripe Connect
 */
function getAssociationStripeAccount($associationId) {
    // Exemple: Mapper les associations à leurs comptes Stripe Connect
    $associations = [
        1 => 'acct_XXXXXXXXXXXXX', // ATDD
        2 => 'acct_YYYYYYYYYYYYY', // Green Tunisia
        3 => 'acct_ZZZZZZZZZZZZZ', // Tunisie Recyclage
        4 => 'acct_AAAAAAAAAAAAA', // EcoAction
    ];
    
    return $associations[$associationId] ?? null;
}
?>
