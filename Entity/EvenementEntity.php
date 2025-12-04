<?php
// Entity/EvenementEntity.php
class EvenementEntity {
    private $id;
    private $titre;
    private $description;
    private $type;
    private $imageMain;
    private $imageSecond;
    private $dateCreation;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    private function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->titre = $data['titre'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->imageMain = $data['image_main'] ?? '';
        $this->imageSecond = $data['image_second'] ?? '';
        $this->dateCreation = $data['date_creation'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTitre() { return $this->titre; }
    public function getDescription() { return $this->description; }
    public function getType() { return $this->type; }
    public function getImageMain() { return $this->imageMain; }
    public function getImageSecond() { return $this->imageSecond; }
    public function getDateCreation() { return $this->dateCreation; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setTitre($titre) { $this->titre = $titre; return $this; }
    public function setDescription($description) { $this->description = $description; return $this; }
    public function setType($type) { $this->type = $type; return $this; }
    public function setImageMain($imageMain) { $this->imageMain = $imageMain; return $this; }
    public function setImageSecond($imageSecond) { $this->imageSecond = $imageSecond; return $this; }
    public function setDateCreation($dateCreation) { $this->dateCreation = $dateCreation; return $this; }

    public function toArray() {
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'description' => $this->description,
            'type' => $this->type,
            'image_main' => $this->imageMain,
            'image_second' => $this->imageSecond,
            'date_creation' => $this->dateCreation
        ];
    }
}
