<?php
// Entity/InscriptionEntity.php
class InscriptionEntity {
    private $id;
    private $evenementId;
    private $nom;
    private $prenom;
    private $age;
    private $email;
    private $tel;
    private $dateInscription;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->hydrate($data);
        }
    }

    private function hydrate($data) {
        $this->id = $data['id'] ?? null;
        $this->evenementId = $data['evenement_id'] ?? null;
        $this->nom = $data['nom'] ?? '';
        $this->prenom = $data['prenom'] ?? '';
        $this->age = $data['age'] ?? null;
        $this->email = $data['email'] ?? '';
        $this->tel = $data['tel'] ?? '';
        $this->dateInscription = $data['date_inscription'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getEvenementId() { return $this->evenementId; }
    public function getNom() { return $this->nom; }
    public function getPrenom() { return $this->prenom; }
    public function getAge() { return $this->age; }
    public function getEmail() { return $this->email; }
    public function getTel() { return $this->tel; }
    public function getDateInscription() { return $this->dateInscription; }

    // Setters
    public function setId($id) { $this->id = $id; return $this; }
    public function setEvenementId($evenementId) { $this->evenementId = $evenementId; return $this; }
    public function setNom($nom) { $this->nom = $nom; return $this; }
    public function setPrenom($prenom) { $this->prenom = $prenom; return $this; }
    public function setAge($age) { $this->age = $age; return $this; }
    public function setEmail($email) { $this->email = $email; return $this; }
    public function setTel($tel) { $this->tel = $tel; return $this; }
    public function setDateInscription($dateInscription) { $this->dateInscription = $dateInscription; return $this; }

    public function toArray() {
        return [
            'id' => $this->id,
            'evenement_id' => $this->evenementId,
            'nom' => $this->nom,
            'prenom' => $this->prenom,
            'age' => $this->age,
            'email' => $this->email,
            'tel' => $this->tel,
            'date_inscription' => $this->dateInscription
        ];
    }
}
