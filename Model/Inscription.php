<?php
// Model/Inscription.php
require_once 'Database.php';
require_once __DIR__ . '/../Entity/InscriptionEntity.php';

class Inscription {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Get all inscriptions as Entity objects
     * @return InscriptionEntity[]
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM inscription ORDER BY date_inscription DESC");
        $results = $stmt->fetchAll();
        
        $entities = [];
        foreach ($results as $row) {
            $entities[] = new InscriptionEntity($row);
        }
        return $entities;
    }

    /**
     * Get inscription by ID as Entity object
     * @param int $id
     * @return InscriptionEntity|null
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM inscription WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        return $result ? new InscriptionEntity($result) : null;
    }

    /**
     * Get inscriptions by event ID
     * @param int $eventId
     * @return InscriptionEntity[]
     */
    public function getByEvent($eventId) {
        $stmt = $this->pdo->prepare("SELECT * FROM inscription WHERE evenement_id = ? ORDER BY date_inscription DESC");
        $stmt->execute([$eventId]);
        $results = $stmt->fetchAll();
        
        $entities = [];
        foreach ($results as $row) {
            $entities[] = new InscriptionEntity($row);
        }
        return $entities;
    }

    /**
     * Create new inscription from Entity
     * @param InscriptionEntity $entity
     * @return bool
     */
    public function create(InscriptionEntity $entity) {
        $stmt = $this->pdo->prepare("INSERT INTO inscription (evenement_id, nom, prenom, age, email, tel) VALUES (?, ?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $entity->getEvenementId(),
            $entity->getNom(),
            $entity->getPrenom(),
            $entity->getAge(),
            $entity->getEmail(),
            $entity->getTel()
        ]);
        
        if ($result) {
            $entity->setId($this->pdo->lastInsertId());
        }
        
        return $result;
    }

    /**
     * Update inscription from Entity
     * @param InscriptionEntity $entity
     * @return bool
     */
    public function update(InscriptionEntity $entity) {
        $stmt = $this->pdo->prepare("UPDATE inscription SET evenement_id=?, nom=?, prenom=?, age=?, email=?, tel=? WHERE id=?");
        return $stmt->execute([
            $entity->getEvenementId(),
            $entity->getNom(),
            $entity->getPrenom(),
            $entity->getAge(),
            $entity->getEmail(),
            $entity->getTel(),
            $entity->getId()
        ]);
    }

    /**
     * Delete inscription by ID
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM inscription WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count total inscriptions
     * @return int
     */
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inscription");
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Count inscriptions for today
     * @return int
     */
    public function countToday() {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM inscription WHERE DATE(date_inscription) = ?");
        $stmt->execute([$today]);
        return (int) $stmt->fetch()['total'];
    }
}
