<?php
// Model/Inscription.php
require_once 'Database.php';

class Inscription {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO inscription (evenement_id, nom, prenom, age, email, tel) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['evenement_id'],
            $data['nom'],
            $data['prenom'],
            $data['age'],
            $data['email'],
            $data['tel']
        ]);
    }

    public function getByEvent($eventId) {
        $stmt = $this->pdo->prepare("SELECT * FROM inscription WHERE evenement_id = ? ORDER BY date_inscription DESC");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM inscription ORDER BY date_inscription DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM inscription WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE inscription SET evenement_id=?, nom=?, prenom=?, age=?, email=?, tel=? WHERE id=?");
        return $stmt->execute([
            $data['evenement_id'],
            $data['nom'],
            $data['prenom'],
            $data['age'],
            $data['email'],
            $data['tel'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM inscription WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
