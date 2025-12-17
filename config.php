<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration directe (sans fichier .env)
// Modifiez ces valeurs selon vos besoins

// Configuration Stripe
// IMPORTANT: Remplacez ces valeurs par vos propres clés Stripe
// Obtenez vos clés sur https://dashboard.stripe.com/apikeys
if (!defined('STRIPE_PUBLIC_KEY')) {
    define('STRIPE_PUBLIC_KEY', 'pk_test_YOUR_PUBLIC_KEY_HERE');
}
if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', 'sk_test_YOUR_SECRET_KEY_HERE');
}

// Configuration du destinataire des paiements
// Par défaut, l'argent va sur votre compte Stripe principal
// Si vous voulez transférer à différentes associations, activez Stripe Connect
define('STRIPE_CONNECT_ENABLED', false); // Mettre à true pour utiliser Stripe Connect

if (!class_exists('Config')) {
    class Config
    {
        private static $pdo = null;

        public static function getConnexion()
        {
            if (!isset(self::$pdo)) {
                $servername = "localhost";
                $username   = "root";
                $password   = "";
                $dbname     = "ecomind";

                try {
                    self::$pdo = new PDO(
                        "mysql:host=$servername;dbname=$dbname",
                        $username,
                        $password
                    );
                    self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
                } catch (Exception $e) {
                    die('Erreur: ' . $e->getMessage());
                }
            }
            return self::$pdo;
        }
    }
}
?>