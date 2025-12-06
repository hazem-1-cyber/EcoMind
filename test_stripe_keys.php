<?php
require_once 'config.php';

echo "<h2>Test des clés Stripe</h2>";

echo "<h3>Clés chargées depuis config.php:</h3>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Constante</th><th>Valeur</th><th>Statut</th></tr>";

// Vérifier STRIPE_PUBLIC_KEY
echo "<tr>";
echo "<td><strong>STRIPE_PUBLIC_KEY</strong></td>";
echo "<td>" . (defined('STRIPE_PUBLIC_KEY') ? substr(STRIPE_PUBLIC_KEY, 0, 20) . '...' : 'NON DÉFINIE') . "</td>";
if (defined('STRIPE_PUBLIC_KEY') && strpos(STRIPE_PUBLIC_KEY, 'pk_test_') === 0 && strlen(STRIPE_PUBLIC_KEY) > 30) {
    echo "<td style='color: green; font-weight: bold;'>✓ VALIDE</td>";
} else {
    echo "<td style='color: red; font-weight: bold;'>✗ INVALIDE</td>";
}
echo "</tr>";

// Vérifier STRIPE_SECRET_KEY
echo "<tr>";
echo "<td><strong>STRIPE_SECRET_KEY</strong></td>";
echo "<td>" . (defined('STRIPE_SECRET_KEY') ? substr(STRIPE_SECRET_KEY, 0, 20) . '...' : 'NON DÉFINIE') . "</td>";
if (defined('STRIPE_SECRET_KEY') && strpos(STRIPE_SECRET_KEY, 'sk_test_') === 0 && strlen(STRIPE_SECRET_KEY) > 30) {
    echo "<td style='color: green; font-weight: bold;'>✓ VALIDE</td>";
} else {
    echo "<td style='color: red; font-weight: bold;'>✗ INVALIDE</td>";
}
echo "</tr>";

echo "</table>";

// Vérifier le fichier .env
echo "<h3>Fichier .env:</h3>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<p style='color: green;'>✓ Le fichier .env existe</p>";
    
    $envContent = file_get_contents($envFile);
    $hasPublicKey = strpos($envContent, 'STRIPE_PUBLIC_KEY=pk_test_') !== false;
    $hasSecretKey = strpos($envContent, 'STRIPE_SECRET_KEY=sk_test_') !== false;
    
    echo "<p>Clé publique dans .env: " . ($hasPublicKey ? "<span style='color: green;'>✓ Trouvée</span>" : "<span style='color: red;'>✗ Non trouvée</span>") . "</p>";
    echo "<p>Clé secrète dans .env: " . ($hasSecretKey ? "<span style='color: green;'>✓ Trouvée</span>" : "<span style='color: red;'>✗ Non trouvée</span>") . "</p>";
} else {
    echo "<p style='color: red;'>✗ Le fichier .env n'existe pas</p>";
}

// Test de connexion à l'API Stripe
echo "<h3>Test de connexion à l'API Stripe:</h3>";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
    
    try {
        \Stripe\Stripe::setApiKey(STRIPE_SECRET_KEY);
        $balance = \Stripe\Balance::retrieve();
        echo "<p style='color: green; font-weight: bold;'>✓ Connexion réussie à l'API Stripe !</p>";
        echo "<p>Devise du compte: " . strtoupper($balance->available[0]->currency ?? 'N/A') . "</p>";
    } catch (\Stripe\Exception\AuthenticationException $e) {
        echo "<p style='color: red; font-weight: bold;'>✗ Erreur d'authentification: " . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p>La clé API Stripe est invalide ou incorrecte.</p>";
    } catch (Exception $e) {
        echo "<p style='color: orange; font-weight: bold;'>⚠ Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Composer vendor non installé. Impossible de tester la connexion.</p>";
    echo "<p>Exécutez: <code>composer install</code></p>";
}

echo "<hr>";
echo "<p><a href='view/FrontOffice/paiement.php'>Tester la page de paiement</a></p>";
?>
