<?php
// Controller/EvenementController.php
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Evenement.php';

/**
 * EvenementController - Contient la logique CRUD pour le FrontOffice
 */
class EvenementController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Afficher tous les événements
     */
    public function index() {
        $events = $this->getAllEvents();
        
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/events.php';
        require __DIR__ . '/../View/templates/footer.php';
    }

    /**
     * Afficher le détail d'un événement
     */
    public function show($id) {
        $event = $this->getEventById($id);
        
        if (!$event) {
            header('HTTP/1.0 404 Not Found');
            echo "Événement introuvable";
            exit;
        }
        
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/event_detail.php';
        require __DIR__ . '/../View/templates/footer.php';
    }

    // ========== CRUD ==========

    /**
     * READ - Récupérer tous les événements
     */
    private function getAllEvents() {
        $stmt = $this->pdo->query("SELECT * FROM evenement ORDER BY date_creation DESC");
        $results = $stmt->fetchAll();
        
        $events = [];
        foreach ($results as $row) {
            $event = new Evenement();
            $event->hydrate($row);
            $events[] = $event;
        }
        return $events;
    }

    /**
     * READ - Récupérer un événement par ID
     */
    private function getEventById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM evenement WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $event = new Evenement();
            $event->hydrate($result);
            return $event;
        }
        return null;
    }
}
