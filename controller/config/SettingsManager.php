<?php
class SettingsManager {
    private $settingsFile;
    
    public function __construct() {
        $this->settingsFile = __DIR__ . '/settings.json';
    }
    
    public function get($key, $default = null) {
        $settings = $this->loadSettings();
        return $settings[$key] ?? $default;
    }
    
    public function set($key, $value) {
        $settings = $this->loadSettings();
        $settings[$key] = $value;
        return $this->saveSettings($settings);
    }
    
    public function getAll() {
        return $this->loadSettings();
    }
    
    public function setMultiple($settings) {
        $currentSettings = $this->loadSettings();
        foreach ($settings as $key => $value) {
            $currentSettings[$key] = $value;
        }
        return $this->saveSettings($currentSettings);
    }
    
    private function loadSettings() {
        if (!file_exists($this->settingsFile)) {
            return [];
        }
        
        $content = file_get_contents($this->settingsFile);
        return json_decode($content, true) ?: [];
    }
    
    private function saveSettings($settings) {
        $content = json_encode($settings, JSON_PRETTY_PRINT);
        return file_put_contents($this->settingsFile, $content) !== false;
    }
    
    // Méthodes spécifiques pour EcoMind
    public function getMinDonationAmount() {
        return $this->get('min_donation_amount', 10);
    }
    
    public function setMinDonationAmount($amount) {
        return $this->set('min_donation_amount', $amount);
    }
    
    public function getCurrency() {
        return $this->get('currency', 'TND');
    }
    
    public function setCurrency($currency) {
        return $this->set('currency', $currency);
    }
    
    public function isAutoValidateEnabled() {
        return $this->get('auto_validate_money', false);
    }
    
    public function setAutoValidate($enabled) {
        return $this->set('auto_validate_money', $enabled);
    }
    
    public function getObjectifMensuel() {
        return $this->get('objectif_mensuel', 10000);
    }
    
    public function setObjectifMensuel($objectif) {
        return $this->set('objectif_mensuel', $objectif);
    }
}
?>