<?php
// Model/Evenement.php
require_once 'Database.php';

class Evenement {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM evenement ORDER BY date_creation DESC");
        return $stmt->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM evenement WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->pdo->prepare("INSERT INTO evenement (titre, description, type, image_main, image_second) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['type'],
            $data['image_main'],
            $data['image_second']
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE evenement SET titre=?, description=?, type=?, image_main=?, image_second=? WHERE id=?");
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['type'],
            $data['image_main'],
            $data['image_second'],
            $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM evenement WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
