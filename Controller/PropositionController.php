<?php
// Controller/PropositionController.php
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Proposition.php';

/**
 * PropositionController - Contient la logique CRUD pour les propositions
 */
class PropositionController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    /**
     * Afficher le formulaire de proposition
     */
    public function form() {
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/proposer_event.php';
        require __DIR__ . '/../View/templates/footer.php';
    }

    /**
     * Traiter la soumission du formulaire
     */
    public function submit() {
        $this->createProposition($_POST);
        
        header('Location: index.php?page=events&msg=proposition_ok');
        exit;
    }

    // ========== CRUD ==========

    /**
     * CREATE - Créer une proposition
     */
    private function createProposition($data) {
        $stmt = $this->pdo->prepare("INSERT INTO proposition (association_nom, email_contact, tel, type, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['association_nom'] ?? '',
            $data['email_contact'] ?? '',
            $data['tel'] ?? '',
            $data['type'] ?? '',
            $data['description'] ?? ''
        ]);
    }
}
