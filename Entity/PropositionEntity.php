<?php
// Entity/PropositionEntity.php
class PropositionEntity {
    private $id;
    private $associationNom;
    private $emailContact;
    private $tel;
    private $type;
    private $description;
    private $dateProposition;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    private function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->associationNom = $data['association_nom'] ?? '';
        $this->emailContact = $data['email_contact'] ?? '';
        $this->tel = $data['tel'] ?? '';
        $this->type = $data['type'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->dateProposition = $data['date_proposition'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getAssociationNom() { return $this->associationNom; }
    public function getEmailContact() { return $this->emailContact; }
    public function getTel() { return $this->tel; }
    public function getType() { return $this->type; }
    public function getDescription() { return $this->description; }
    public function getDateProposition() { return $this->dateProposition; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setAssociationNom($associationNom) { $this->associationNom = $associationNom; return $this; }
    public function setEmailContact($emailContact) { $this->emailContact = $emailContact; return $this; }
    public function setTel($tel) { $this->tel = $tel; return $this; }
    public function setType($type) { $this->type = $type; return $this; }
    public function setDescription($description) { $this->description = $description; return $this; }
    public function setDateProposition($dateProposition) { $this->dateProposition = $dateProposition; return $this; }

    public function toArray() {
        return [
            'id' => $this->id,
            'association_nom' => $this->associationNom,
            'email_contact' => $this->emailContact,
            'tel' => $this->tel,
            'type' => $this->type,
            'description' => $this->description,
            'date_proposition' => $this->dateProposition
        ];
    }
}
