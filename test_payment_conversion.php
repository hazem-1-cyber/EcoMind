<?php
// Test de conversion TND vers USD pour les paiements Stripe

echo "<h2>Test de conversion TND → USD</h2>";

$taux = 3.1; // 1 USD ≈ 3.1 TND

$montantsTND = [10, 50, 100, 500, 1000];

echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
echo "<tr style='background: #f0f0f0;'>";
echo "<th>Montant TND</th>";
echo "<th>Montant USD</th>";
echo "<th>Montant Stripe (centimes)</th>";
echo "<th>Vérification</th>";
echo "</tr>";

foreach ($montantsTND as $tnd) {
    $usd = $tnd / $taux;
    $stripeCents = intval($usd * 100);
    $verification = $stripeCents / 100;
    
    echo "<tr>";
    echo "<td>" . number_format($tnd, 2) . " TND</td>";
    echo "<td>" . number_format($usd, 2) . " USD</td>";
    echo "<td>" . $stripeCents . " cents</td>";
    echo "<td>" . number_format($verification, 2) . " USD</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h3>Exemple de validation</h3>";
$montantTest = 100; // TND
$montantUSD = $montantTest / $taux;
$stripeCents = intval($montantUSD * 100);
$montantStripeUSD = $stripeCents / 100;

echo "<p><strong>Montant original:</strong> {$montantTest} TND</p>";
echo "<p><strong>Converti en USD:</strong> " . number_format($montantUSD, 2) . " USD</p>";
echo "<p><strong>Envoyé à Stripe:</strong> {$stripeCents} cents</p>";
echo "<p><strong>Reçu de Stripe:</strong> " . number_format($montantStripeUSD, 2) . " USD</p>";

$tolerance = $montantUSD * 0.01; // 1% de tolérance
$difference = abs($montantStripeUSD - $montantUSD);

echo "<p><strong>Différence:</strong> " . number_format($difference, 4) . " USD</p>";
echo "<p><strong>Tolérance:</strong> " . number_format($tolerance, 4) . " USD</p>";

if ($difference <= $tolerance) {
    echo "<p style='color: green; font-weight: bold;'>✓ Validation OK</p>";
} else {
    echo "<p style='color: red; font-weight: bold;'>✗ Validation échouée</p>";
}

echo "<hr>";
echo "<h3>Simulation de session</h3>";
echo "<pre>";
$sessionData = [
    'don_data' => [
        'type_don' => 'money',
        'email' => 'test@example.com',
        'association_id' => 1,
        'montant' => 100.00,
        'livraison' => 'en_ligne'
    ]
];
print_r($sessionData);
echo "</pre>";

echo "<p><strong>Montant à envoyer à Stripe:</strong> " . intval(($sessionData['don_data']['montant'] / $taux) * 100) . " cents USD</p>";
?>
