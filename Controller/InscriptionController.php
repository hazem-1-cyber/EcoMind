<?php
// Controller/InscriptionController.php
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/EmailService.php';

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
        $success = $this->createInscription($_POST);
        
        if ($success) {
            // Envoyer l'email de bienvenue et confirmation
            $this->sendWelcomeEmail($_POST);
        }
        
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
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            $event = new Evenement();
            $event->hydrate($result);
            return $event;
        }
        return null;
    }

    /**
     * Envoyer l'email de bienvenue et confirmation
     */
    private function sendWelcomeEmail($data) {
        try {
            // Récupérer les informations de l'événement
            $event = $this->getEventById($data['evenement_id']);
            if (!$event) {
                error_log("Impossible de récupérer l'événement pour l'email de confirmation");
                return false;
            }
            
            // Créer le service email et envoyer
            $emailService = new EmailService();
            $participantName = trim(($data['prenom'] ?? '') . ' ' . ($data['nom'] ?? ''));
            $eventTitle = $event->getTitre();
            $participantEmail = $data['email'] ?? '';
            
            if (empty($participantEmail)) {
                error_log("Email du participant manquant pour l'envoi de confirmation");
                return false;
            }
            
            $result = $emailService->sendWelcomeConfirmation(
                $participantEmail,
                $participantName,
                $eventTitle
            );
            
            if ($result) {
                error_log("Email de bienvenue envoyé avec succès à: $participantEmail");
            } else {
                error_log("Échec de l'envoi de l'email de bienvenue à: $participantEmail");
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email de bienvenue: " . $e->getMessage());
            return false;
        }
    }
}