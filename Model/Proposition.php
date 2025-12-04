<?php
// Model/Proposition.php
require_once 'Database.php';
require_once __DIR__ . '/../Entity/PropositionEntity.php';

class Proposition {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Get all propositions as Entity objects
     * @return PropositionEntity[]
     */
    public function getAll() {
        $stmt = $this->pdo->query("SELECT * FROM proposition ORDER BY date_proposition DESC");
        $results = $stmt->fetchAll();
        
        $entities = [];
        foreach ($results as $row) {
            $entities[] = new PropositionEntity($row);
        }
        return $entities;
    }

    /**
     * Get proposition by ID as Entity object
     * @param int $id
     * @return PropositionEntity|null
     */
    public function getById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proposition WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        return $result ? new PropositionEntity($result) : null;
    }

    /**
     * Create new proposition from Entity
     * @param PropositionEntity $entity
     * @return bool
     */
    public function create(PropositionEntity $entity) {
        $stmt = $this->pdo->prepare("INSERT INTO proposition (association_nom, email_contact, tel, type, description) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([
            $entity->getAssociationNom(),
            $entity->getEmailContact(),
            $entity->getTel(),
            $entity->getType(),
            $entity->getDescription()
        ]);
        
        if ($result) {
            $entity->setId($this->pdo->lastInsertId());
        }
        
        return $result;
    }

    /**
     * Update proposition from Entity
     * @param PropositionEntity $entity
     * @return bool
     */
    public function update(PropositionEntity $entity) {
        $stmt = $this->pdo->prepare("UPDATE proposition SET association_nom=?, email_contact=?, tel=?, type=?, description=? WHERE id=?");
        return $stmt->execute([
            $entity->getAssociationNom(),
            $entity->getEmailContact(),
            $entity->getTel(),
            $entity->getType(),
            $entity->getDescription(),
            $entity->getId()
        ]);
    }

    /**
     * Delete proposition by ID
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM proposition WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Count total propositions
     * @return int
     */
    public function count() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM proposition");
        return (int) $stmt->fetch()['total'];
    }
}
