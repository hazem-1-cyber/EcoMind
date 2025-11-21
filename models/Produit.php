<?php
require_once(__DIR__ . '/../config/database.php');
class Produit {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM produits");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM produits WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($nom, $description, $prix, $image) {
        $stmt = $this->conn->prepare("INSERT INTO produits (nom, description, prix, image) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$nom, $description, $prix, $image]);
    }
    public function update($id, $nom, $description, $prix, $image) {
        $stmt = $this->conn->prepare("UPDATE produits SET nom = ?, description = ?, prix = ?, image = ? WHERE id = ?");
        return $stmt->execute([$nom, $description, $prix, $image, $id]);
    }
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM produits WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

?>
