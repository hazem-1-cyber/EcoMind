<?php
// Model/Evenement.php
require_once 'Database.php';
require_once __DIR__ . '/../Entity/EvenementEntity.php';

class Evenement {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Get all events as Entity objects
     * @return EvenementEntity[]
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM evenement ORDER BY date_creation DESC");
        $results = $stmt->fetchAll();
        
        $entities = [];
        foreach ($results as $row) {
            $entities[] = new EvenementEntity($row);
        }
        return $entities;
    }

    /**
     * Get event by ID as Entity object
     * @param int $id
     * @return EvenementEntity|null
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM evenement WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        return $result ? new EvenementEntity($result) : null;
    }

    /**
     * Create new event from Entity
     * @param EvenementEntity $entity
     * @return bool
     */
    public function create(EvenementEntity $entity) {
        $stmt = $this->pdo->prepare("INSERT INTO evenement (titre, description, type, image_main, image_second) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $entity->getTitre(),
            $entity->getDescription(),
            $entity->getType(),
            $entity->getImageMain(),
            $entity->getImageSecond()
        ]);
        
        if ($result) {
            $entity->setId($this->pdo->lastInsertId());
        }
        
        return $result;
    }

    /**
     * Update event from Entity
     * @param EvenementEntity $entity
     * @return bool
     */
    public function update(EvenementEntity $entity) {
        $stmt = $this->pdo->prepare("UPDATE evenement SET titre=?, description=?, type=?, image_main=?, image_second=? WHERE id=?");
        return $stmt->execute([
            $entity->getTitre(),
            $entity->getDescription(),
            $entity->getType(),
            $entity->getImageMain(),
            $entity->getImageSecond(),
            $entity->getId()
        ]);
    }

    /**
     * Delete event by ID
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM evenement WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count total events
     * @return int
     */
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM evenement");
        return (int) $stmt->fetch()['total'];
    }
}
