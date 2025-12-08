<?php
// Controller/InscriptionController.php
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Evenement.php';

/**
 * InscriptionController - Contient la logique CRUD pour les inscriptions
 */
class InscriptionController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function form($eventId) {
        $event = $this->getEventById($eventId);
        
        if (!$event) {
            echo "Événement introuvable";
            exit;
        }
        
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/inscription.php';
        require __DIR__ . '/../View/templates/footer.php';
    }

    /**
     * Traiter la soumission du formulaire
     */
    public function submit() {
        $this->createInscription($_POST);
        
        header('Location: index.php?page=events&msg=inscription_ok');
        exit;
    }

    // ========== CRUD ==========

    /**
     * CREATE - Créer une inscription
     */
    private function createInscription($data) {
        $stmt = $this->pdo->prepare("INSERT INTO inscription (evenement_id, nom, prenom, age, email, tel) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['evenement_id'] ?? '',
            $data['nom'] ?? '',
            $data['prenom'] ?? '',
            $data['age'] ?? '',
            $data['email'] ?? '',
            $data['tel'] ?? ''
        ]);
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
