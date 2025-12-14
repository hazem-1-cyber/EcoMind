<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration directe (sans fichier .env)
// Modifiez ces valeurs selon vos besoins

// Configuration Stripe - REMPLACEZ PAR VOS CLÉS DE TEST
// Obtenez vos clés sur : https://dashboard.stripe.com/test/apikeys
if (!defined('STRIPE_PUBLIC_KEY')) {
    define('STRIPE_PUBLIC_KEY', 'pk_test_REMPLACEZ_PAR_VOTRE_CLE_PUBLIQUE');
}
if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', 'sk_test_REMPLACEZ_PAR_VOTRE_CLE_SECRETE');
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