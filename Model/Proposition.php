<?php
// Model/Proposition.php

/**
 * Classe Proposition - Représente une proposition
 */
class Proposition {
    // Propriétés privées (encapsulation)
    private $id;
    private $associationNom;
    private $emailContact;
    private $tel;
    private $type;
    private $description;
    private $dateProposition;

    // ========== GETTERS ==========
    
    public function getId() {
        return $this->id;
    }

    public function getAssociationNom() {
        return $this->associationNom;
    }

    public function getEmailContact() {
        return $this->emailContact;
    }

    public function getTel() {
        return $this->tel;
    }

    public function getType() {
        return $this->type;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getDateProposition() {
        return $this->dateProposition;
    }

    // ========== SETTERS ==========
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setAssociationNom($associationNom) {
        $this->associationNom = $associationNom;
        return $this;
    }

    public function setEmailContact($emailContact) {
        $this->emailContact = $emailContact;
        return $this;
    }

    public function setTel($tel) {
        $this->tel = $tel;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function setDateProposition($dateProposition) {
        $this->dateProposition = $dateProposition;
        return $this;
    }

    /**
     * Hydrater l'objet à partir d'un tableau
     */
    public function hydrate($data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['association_nom'])) $this->associationNom = $data['association_nom'];
        if (isset($data['email_contact'])) $this->emailContact = $data['email_contact'];
        if (isset($data['tel'])) $this->tel = $data['tel'];
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['date_proposition'])) $this->dateProposition = $data['date_proposition'];
        return $this;
    }
}
