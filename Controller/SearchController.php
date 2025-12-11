<?php
// Controller/SearchController.php
require_once __DIR__ . '/../Model/Database.php';

/**
 * SearchController - Recherche et Filtrage Avancé
 */
class SearchController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Recherche d'événements avec filtres avancés
     */
    public function searchEvents($filters = []) {
        // Debug logging
        error_log("🔍 SearchController - searchEvents called with filters: " . print_r($filters, true));
        
        // Start with basic query
        $sql = "SELECT e.*, 
                (SELECT COUNT(*) FROM inscription i WHERE i.evenement_id = e.id) as nb_inscriptions 
                FROM evenement e";
        
        $conditions = [];
        $params = [];
        
        // Filtre par mot-clé (titre ou description)
        if (isset($filters['keyword']) && trim($filters['keyword']) !== '') {
            $keyword = trim($filters['keyword']);
            $conditions[] = "(e.titre LIKE ? OR e.description LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        // Filtre par type
        if (!empty($filters['type']) && $filters['type'] !== '') {
            $conditions[] = "e.type = ?";
            $params[] = $filters['type'];
            error_log("🔍 Adding type filter: " . $filters['type']);
        }
        
        // Filtre par date de création
        if (!empty($filters['date_from']) && $filters['date_from'] !== '') {
            $conditions[] = "DATE(e.date_creation) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to']) && $filters['date_to'] !== '') {
            $conditions[] = "DATE(e.date_creation) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Ajouter les conditions WHERE
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Tri dynamique
        $orderBy = $this->buildOrderBy($filters['sort'] ?? 'date_desc');
        $sql .= " ORDER BY " . $orderBy;
        
        // Limite pour pagination
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . intval($filters['offset']);
            }
        }
        
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $results = $stmt->fetchAll();
            
            return $results;
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Recherche d'inscriptions avec filtres avancés
     */
    public function searchInscriptions($filters = []) {
        $sql = "SELECT i.*, e.titre as evenement_titre, e.type as evenement_type 
                FROM inscription i 
                JOIN evenement e ON i.evenement_id = e.id";
        
        $conditions = [];
        $params = [];
        
        // Filtre par nom/prénom
        if (!empty($filters['keyword']) && trim($filters['keyword']) !== '') {
            $keyword = trim($filters['keyword']);
            $conditions[] = "(i.nom LIKE ? OR i.prenom LIKE ? OR i.email LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        // Filtre par événement
        if (!empty($filters['evenement_id'])) {
            $conditions[] = "i.evenement_id = ?";
            $params[] = $filters['evenement_id'];
        }
        
        // Filtre par âge
        if (!empty($filters['age_min'])) {
            $conditions[] = "i.age >= ?";
            $params[] = intval($filters['age_min']);
        }
        
        if (!empty($filters['age_max'])) {
            $conditions[] = "i.age <= ?";
            $params[] = intval($filters['age_max']);
        }
        
        // Filtre par date d'inscription
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(i.date_inscription) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(i.date_inscription) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Ajouter les conditions WHERE
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Tri dynamique
        $orderBy = $this->buildOrderByInscriptions($filters['sort'] ?? 'date_desc');
        $sql .= " ORDER BY " . $orderBy;
        
        // Limite pour pagination
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . intval($filters['limit']);
            if (!empty($filters['offset'])) {
                $sql .= " OFFSET " . intval($filters['offset']);
            }
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Recherche de propositions avec filtres
     */
    public function searchPropositions($filters = []) {
        $sql = "SELECT * FROM proposition";
        
        $conditions = [];
        $params = [];
        
        // Filtre par mot-clé
        if (!empty($filters['keyword']) && trim($filters['keyword']) !== '') {
            $keyword = trim($filters['keyword']);
            $conditions[] = "(association_nom LIKE ? OR description LIKE ? OR email_contact LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        // Filtre par type
        if (!empty($filters['type'])) {
            $conditions[] = "type = ?";
            $params[] = $filters['type'];
        }
        
        // Filtre par date
        if (!empty($filters['date_from'])) {
            $conditions[] = "DATE(date_proposition) >= ?";
            $params[] = $filters['date_from'];
        }
        
        if (!empty($filters['date_to'])) {
            $conditions[] = "DATE(date_proposition) <= ?";
            $params[] = $filters['date_to'];
        }
        
        // Ajouter les conditions WHERE
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        // Tri
        $orderBy = $this->buildOrderByPropositions($filters['sort'] ?? 'date_desc');
        $sql .= " ORDER BY " . $orderBy;
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /**
     * Autocomplete pour les événements
     */
    public function getEventSuggestions($query) {
        $sql = "SELECT DISTINCT titre as suggestion, 'titre' as type FROM evenement WHERE titre LIKE ?
                UNION
                SELECT DISTINCT type as suggestion, 'type' as type FROM evenement WHERE type LIKE ?
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['%' . $query . '%', '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Autocomplete pour les inscriptions
     */
    public function getInscriptionSuggestions($query) {
        $sql = "SELECT DISTINCT CONCAT(nom, ' ', prenom) as suggestion, 'nom' as type FROM inscription 
                WHERE nom LIKE ? OR prenom LIKE ?
                UNION
                SELECT DISTINCT email as suggestion, 'email' as type FROM inscription WHERE email LIKE ?
                LIMIT 10";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['%' . $query . '%', '%' . $query . '%', '%' . $query . '%']);
        return $stmt->fetchAll();
    }

    /**
     * Obtenir tous les types d'événements pour les filtres
     */
    public function getEventTypes() {
        $sql = "SELECT DISTINCT type FROM evenement WHERE type IS NOT NULL ORDER BY type";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Obtenir tous les événements pour les filtres d'inscription
     */
    public function getEventsForFilter() {
        $sql = "SELECT id, titre FROM evenement ORDER BY titre";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Construire la clause ORDER BY pour les événements
     */
    private function buildOrderBy($sort) {
        switch ($sort) {
            case 'title_asc':
                return 'e.titre ASC';
            case 'title_desc':
                return 'e.titre DESC';
            case 'type_asc':
                return 'e.type ASC, e.titre ASC';
            case 'type_desc':
                return 'e.type DESC, e.titre ASC';
            case 'popularity_asc':
                return 'nb_inscriptions ASC, e.titre ASC';
            case 'popularity_desc':
                return 'nb_inscriptions DESC, e.titre ASC';
            case 'date_asc':
                return 'e.date_creation ASC';
            case 'date_desc':
            default:
                return 'e.date_creation DESC';
        }
    }

    /**
     * Construire la clause ORDER BY pour les inscriptions
     */
    private function buildOrderByInscriptions($sort) {
        switch ($sort) {
            case 'name_asc':
                return 'i.nom ASC, i.prenom ASC';
            case 'name_desc':
                return 'i.nom DESC, i.prenom DESC';
            case 'age_asc':
                return 'i.age ASC, i.nom ASC';
            case 'age_desc':
                return 'i.age DESC, i.nom ASC';
            case 'event_asc':
                return 'e.titre ASC, i.nom ASC';
            case 'event_desc':
                return 'e.titre DESC, i.nom ASC';
            case 'date_asc':
                return 'i.date_inscription ASC';
            case 'date_desc':
            default:
                return 'i.date_inscription DESC';
        }
    }

    /**
     * Construire la clause ORDER BY pour les propositions
     */
    private function buildOrderByPropositions($sort) {
        switch ($sort) {
            case 'association_asc':
                return 'association_nom ASC';
            case 'association_desc':
                return 'association_nom DESC';
            case 'type_asc':
                return 'type ASC, association_nom ASC';
            case 'type_desc':
                return 'type DESC, association_nom ASC';
            case 'date_asc':
                return 'date_proposition ASC';
            case 'date_desc':
            default:
                return 'date_proposition DESC';
        }
    }

    /**
     * Compter le total des résultats pour pagination
     */
    public function countSearchResults($type, $filters = []) {
        switch ($type) {
            case 'events':
                return $this->countEvents($filters);
            case 'inscriptions':
                return $this->countInscriptions($filters);
            case 'propositions':
                return $this->countPropositions($filters);
            default:
                return 0;
        }
    }

    private function countEvents($filters) {
        $sql = "SELECT COUNT(DISTINCT e.id) as total FROM evenement e";
        $conditions = [];
        $params = [];
        
        if (!empty($filters['keyword'])) {
            $conditions[] = "(e.titre LIKE ? OR e.description LIKE ?)";
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }
        
        if (!empty($filters['type'])) {
            $conditions[] = "e.type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    private function countInscriptions($filters) {
        $sql = "SELECT COUNT(i.id) as total FROM inscription i JOIN evenement e ON i.evenement_id = e.id";
        $conditions = [];
        $params = [];
        
        if (!empty($filters['keyword'])) {
            $conditions[] = "(i.nom LIKE ? OR i.prenom LIKE ? OR i.email LIKE ?)";
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }
        
        if (!empty($filters['evenement_id'])) {
            $conditions[] = "i.evenement_id = ?";
            $params[] = $filters['evenement_id'];
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }

    private function countPropositions($filters) {
        $sql = "SELECT COUNT(*) as total FROM proposition";
        $conditions = [];
        $params = [];
        
        if (!empty($filters['keyword'])) {
            $conditions[] = "(association_nom LIKE ? OR description LIKE ?)";
            $params[] = '%' . $filters['keyword'] . '%';
            $params[] = '%' . $filters['keyword'] . '%';
        }
        
        if (!empty($filters['type'])) {
            $conditions[] = "type = ?";
            $params[] = $filters['type'];
        }
        
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
}