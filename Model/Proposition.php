<?php
// Model/Proposition.php
require_once 'Database.php';

class Proposition {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO proposition (association_nom, email_contact, tel, type, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['association_nom'],
            $data['email_contact'],
            $data['tel'],
            $data['type'],
            $data['description']
        ]);
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM proposition ORDER BY date_proposition DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proposition WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE proposition SET association_nom=?, email_contact=?, tel=?, type=?, description=? WHERE id=?");
        return $stmt->execute([
            $data['association_nom'],
            $data['email_contact'],
            $data['tel'],
            $data['type'],
            $data['description'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM proposition WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
