<?php
require_once(__DIR__ . '/../config/database.php');

class Stock {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM stocks");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByDepot($depot_id) {
        $stmt = $this->conn->prepare("SELECT * FROM stocks WHERE depot_id = ?");
        $stmt->execute([$depot_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByProduit($produit_id) {
        $stmt = $this->conn->prepare("SELECT * FROM stocks WHERE produit_id = ?");
        $stmt->execute([$produit_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOne($depot_id, $produit_id) {
        $stmt = $this->conn->prepare("SELECT * FROM stocks WHERE depot_id = ? AND produit_id = ?");
        $stmt->execute([$depot_id, $produit_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

       public function updateStock($depot_id, $produit_id, $quantite) {
        $stmt = $this->conn->prepare("UPDATE stocks SET quantite = ? WHERE depot_id = ? AND produit_id = ?");
        return $stmt->execute([$quantite, $depot_id, $produit_id]);
    }


    public function addStock($depot_id, $produit_id, $quantite): mixed {
        $stmt = $this->conn->prepare("INSERT INTO stocks (depot_id, produit_id, quantite) VALUES (?, ?, ?)");
        return $stmt->execute([$depot_id, $produit_id, $quantite]);
    }
}
?>
