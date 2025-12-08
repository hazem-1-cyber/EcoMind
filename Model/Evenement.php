<?php
// Model/Evenement.php

/**
 * Classe Evenement - Représente un événement
 */
class Evenement {
    // Propriétés privées (encapsulation)
    private $id;
    private $titre;
    private $description;
    private $type;
    private $imageMain;
    private $imageSecond;
    private $dateCreation;

    // ========== GETTERS ==========
    
    public function getId() {
        return $this->id;
    }

    public function getTitre() {
        return $this->titre;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getType() {
        return $this->type;
    }

    public function getImageMain() {
        return $this->imageMain;
    }

    public function getImageSecond() {
        return $this->imageSecond;
    }

    public function getDateCreation() {
        return $this->dateCreation;
    }

    // ========== SETTERS ==========
    
    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setTitre($titre) {
        $this->titre = $titre;
        return $this;
    }

    public function setDescription($description) {
        $this->description = $description;
        return $this;
    }

    public function setType($type) {
        $this->type = $type;
        return $this;
    }

    public function setImageMain($imageMain) {
        $this->imageMain = $imageMain;
        return $this;
    }

    public function setImageSecond($imageSecond) {
        $this->imageSecond = $imageSecond;
        return $this;
    }

    public function setDateCreation($dateCreation) {
        $this->dateCreation = $dateCreation;
        return $this;
    }

    /**
     * Hydrater l'objet à partir d'un tableau
     */
    public function hydrate($data) {
        if (isset($data['id'])) $this->id = $data['id'];
        if (isset($data['titre'])) $this->titre = $data['titre'];
        if (isset($data['description'])) $this->description = $data['description'];
        if (isset($data['type'])) $this->type = $data['type'];
        if (isset($data['image_main'])) $this->imageMain = $data['image_main'];
        if (isset($data['image_second'])) $this->imageSecond = $data['image_second'];
        if (isset($data['date_creation'])) $this->dateCreation = $data['date_creation'];
        return $this;
    }
}
