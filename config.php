<?php
// Activer l'affichage des erreurs pour le débogage
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Configuration directe (sans fichier .env)
// Modifiez ces valeurs selon vos besoins

// Configuration Stripe - Remplacez par vos vraies clés
if (!defined('STRIPE_PUBLIC_KEY')) {
    define('STRIPE_PUBLIC_KEY', 'pk_test_VOTRE_CLE_PUBLIQUE_ICI');
}
if (!defined('STRIPE_SECRET_KEY')) {
    define('STRIPE_SECRET_KEY', 'sk_test_VOTRE_CLE_SECRETE_ICI');
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