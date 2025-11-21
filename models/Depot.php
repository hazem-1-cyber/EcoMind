<?php
require_once(__DIR__ . '/../config/database.php');
class Depot {
    private $conn;
    public function __construct() {
        $db = new Database();
        $this->conn = $db->connect();
    }
    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM depots");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM depots WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function create($region) {
        $stmt = $this->conn->prepare("INSERT INTO depots (region) VALUES (?)");
        return $stmt->execute([$region]);
    }
    public function update($id, $region) {
        $stmt = $this->conn->prepare("UPDATE depots SET region = ? WHERE id = ?");
        return $stmt->execute([$region, $id]);
    }
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM depots WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
