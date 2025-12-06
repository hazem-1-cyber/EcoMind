<?php
/**
 * Gestionnaire de paramètres en temps réel
 * Utilise un fichier JSON pour stocker les paramètres
 */
class SettingsManager {
    private $settingsFile;
    private $settings;

    public function __construct() {
        $this->settingsFile = __DIR__ . '/settings.json';
        $this->loadSettings();
    }

    /**
     * Charger les paramètres depuis le fichier JSON
     */
    private function loadSettings() {
        if (file_exists($this->settingsFile)) {
            $json = file_get_contents($this->settingsFile);
            $this->settings = json_decode($json, true);
        } else {
            // Paramètres par défaut
            $this->settings = [
                'notifications_enabled' => true,
                'email_notifications' => true,
                'auto_validate_money' => false,
                'min_donation_amount' => 10,
                'currency' => 'TND'
            ];
            $this->saveSettings();
        }
    }

    /**
     * Sauvegarder les paramètres dans le fichier JSON
     */
    private function saveSettings() {
        $json = json_encode($this->settings, JSON_PRETTY_PRINT);
        file_put_contents($this->settingsFile, $json);
    }

    /**
     * Obtenir un paramètre
     */
    public function get($key, $default = null) {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Obtenir tous les paramètres
     */
    public function getAll() {
        return $this->settings;
    }

    /**
     * Définir un paramètre
     */
    public function set($key, $value) {
        $this->settings[$key] = $value;
        $this->saveSettings();
    }

    /**
     * Définir plusieurs paramètres
     */
    public function setMultiple($data) {
        foreach ($data as $key => $value) {
            $this->settings[$key] = $value;
        }
        $this->saveSettings();
    }

    /**
     * Obtenir le montant minimum de don
     */
    public function getMinDonationAmount() {
        return (float) $this->get('min_donation_amount', 10);
    }

    /**
     * Vérifier si la validation automatique est activée
     */
    public function isAutoValidateEnabled() {
        return (bool) $this->get('auto_validate_money', false);
    }

    /**
     * Obtenir la devise
     */
    public function getCurrency() {
        return $this->get('currency', 'TND');
    }
}
?>
