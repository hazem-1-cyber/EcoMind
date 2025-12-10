<?php
// config.php
class Config {
    private static ?PDO $pdo = null;

    public static function getConnexion(): PDO {
        if (self::$pdo === null) {
            try {
                self::$pdo = new PDO(
                    'mysql:host=localhost;dbname=ecomind;charset=utf8mb4',
                    'root',
                    '',
                    [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                    ]
                );
            } catch (Exception $e) {
                die('Connexion échouée : ' . $e->getMessage());
            }
        }
        return self::$pdo;
    }
}

config::getConnexion();
?>