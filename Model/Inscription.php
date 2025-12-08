<?php
// Model/Inscription.php

/**
 * Classe Inscription - Représente une inscription
 */
class Inscription {
    // Propriétés privées (encapsulation)
    private $id;
    private $evenementId;
    private $nom;
    private $prenom;
    private $age;
    private $email;
    private $tel;
    private $dateInscription;

    // ========== GETTERS ==========
    
    public function getId() {
        return $this->id;
    }

    public function getEvenementId() {
        return $this->evenementId;
    }

    public function getNom() {
        return $this->nom;
    }

    public function getPrenom() {
        return $this->prenom;
    }

    public function getAge() {
        return $this->age;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTel() {
        return $this->tel;
    }

    public function getDateInscription() {
        return $this->dateInscription;
    }

    // ========== SETTERS ==========
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setEvenementId($evenementId) {
        $this->evenementId = $evenementId;
        return $this;
    }

    public function setNom($nom) {
        $this->nom = $nom;
        return $this;
    }

    public function setPrenom($prenom) {
        $this->prenom = $prenom;
        return $this;
    }

    public function setAge($age) {
        $this->age = $age;
        return $this;
    }

    public function setEmail($email) {
        $this->email = $email;
        return $this;
    }

    public function setTel($tel) {
        $this->tel = $tel;
        return $this;
    }

    public function setDateInscription($dateInscription) {
        $this->dateInscription = $dateInscription;
        return $this;
    }

    /**
     * Hydrater l'objet à partir d'un tableau
     */
    public function hydrate($data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['evenement_id'])) $this->evenementId = $data['evenement_id'];
        if (isset($data['nom'])) $this->nom = $data['nom'];
        if (isset($data['prenom'])) $this->prenom = $data['prenom'];
        if (isset($data['age'])) $this->age = $data['age'];
        if (isset($data['email'])) $this->email = $data['email'];
        if (isset($data['tel'])) $this->tel = $data['tel'];
        if (isset($data['date_inscription'])) $this->dateInscription = $data['date_inscription'];
        return $this;
    }
}
