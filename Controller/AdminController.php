<?php
// Controller/AdminController.php
require_once __DIR__ . '/../Model/Database.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Proposition.php';

/**
 * AdminController - Contient TOUTE la logique CRUD avec PDO
 */
class AdminController {
    private $pdo;

    public function __construct() {
        $this->pdo = Database::getPdo();
    }

    // ========== DASHBOARD ==========
    
    public function dashboard() {
        // Gérer les suppressions
        if (isset($_GET['delete_event'])) {
            $this->deleteEvent($_GET['delete_event']);
            header('Location: index.php?page=admin_dashboard');
            exit;
        }
        
        if (isset($_GET['delete_inscription'])) {
            $this->deleteInscription($_GET['delete_inscription']);
            header('Location: index.php?page=admin_dashboard');
            exit;
        }
        
        if (isset($_GET['delete_proposition'])) {
            $this->deleteProposition($_GET['delete_proposition']);
            header('Location: index.php?page=admin_dashboard');
            exit;
        }

        // Récupérer toutes les données
        $events = $this->getAllEvents();
        $inscriptions = $this->getAllInscriptions();
        $propositions = $this->getAllPropositions();
        
        // Calculer les statistiques
        $stats = [
            'totalEvents' => $this->countEvents(),
            'totalInscriptions' => $this->countInscriptions(),
            'inscriptionsToday' => $this->countInscriptionsToday(),
            'totalPropositions' => $this->countPropositions()
        ];
        
        require __DIR__ . '/../View/BackOffice/dashboard.php';
    }

    // ========== CRUD ÉVÉNEMENTS ==========
    
    /**
     * CREATE - Créer un événement
     */
    private function createEvent($data) {
        $stmt = $this->pdo->prepare("INSERT INTO evenement (titre, description, type, image_main, image_second) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['type'],
            $data['image_main'],
            $data['image_second']
        ]);
    }

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

    /**
     * UPDATE - Mettre à jour un événement
     */
    private function updateEvent($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE evenement SET titre=?, description=?, type=?, image_main=?, image_second=? WHERE id=?");
        return $stmt->execute([
            $data['titre'],
            $data['description'],
            $data['type'],
            $data['image_main'],
            $data['image_second'],
            $id
        ]);
    }

    /**
     * DELETE - Supprimer un événement
     */
    private function deleteEvent($id) {
        $stmt = $this->pdo->prepare("DELETE FROM evenement WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * COUNT - Compter les événements
     */
    private function countEvents() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM evenement");
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Gérer les événements (formulaire)
     */
    public function events() {
        if (isset($_GET['delete'])) {
            $this->deleteEvent($_GET['delete']);
            header('Location: index.php?page=admin_events');
            exit;
        }

        if (isset($_GET['edit'])) {
            $event = $this->getEventById($_GET['edit']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateEvent($_GET['edit'], $_POST);
                header('Location: index.php?page=admin_dashboard#events');
                exit;
            }
            
            require __DIR__ . '/../View/BackOffice/admin_events_form.php';
            exit;
        }

        if (isset($_GET['add'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->createEvent($_POST);
                header('Location: index.php?page=admin_dashboard#events');
                exit;
            }
            
            $event = null;
            require __DIR__ . '/../View/BackOffice/admin_events_form.php';
            exit;
        }

        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    // ========== CRUD INSCRIPTIONS ==========
    
    /**
     * CREATE - Créer une inscription
     */
    private function createInscription($data) {
        $stmt = $this->pdo->prepare("INSERT INTO inscription (evenement_id, nom, prenom, age, email, tel) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['evenement_id'],
            $data['nom'],
            $data['prenom'],
            $data['age'],
            $data['email'],
            $data['tel']
        ]);
    }

    /**
     * READ - Récupérer toutes les inscriptions
     */
    private function getAllInscriptions() {
        $stmt = $this->pdo->query("SELECT * FROM inscription ORDER BY date_inscription DESC");
        $results = $stmt->fetchAll();
        
        $inscriptions = [];
        foreach ($results as $row) {
            $inscription = new Inscription();
            $inscription->hydrate($row);
            $inscriptions[] = $inscription;
        }
        return $inscriptions;
    }

    /**
     * READ - Récupérer une inscription par ID
     */
    private function getInscriptionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM inscription WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $inscription = new Inscription();
            $inscription->hydrate($result);
            return $inscription;
        }
        return null;
    }

    /**
     * UPDATE - Mettre à jour une inscription
     */
    private function updateInscription($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE inscription SET evenement_id=?, nom=?, prenom=?, age=?, email=?, tel=? WHERE id=?");
        return $stmt->execute([
            $data['evenement_id'],
            $data['nom'],
            $data['prenom'],
            $data['age'],
            $data['email'],
            $data['tel'],
            $id
        ]);
    }

    /**
     * DELETE - Supprimer une inscription
     */
    private function deleteInscription($id) {
        $stmt = $this->pdo->prepare("DELETE FROM inscription WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * COUNT - Compter les inscriptions
     */
    private function countInscriptions() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM inscription");
        return (int) $stmt->fetch()['total'];
    }

    /**
     * COUNT - Compter les inscriptions d'aujourd'hui
     */
    private function countInscriptionsToday() {
        $today = date('Y-m-d');
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM inscription WHERE DATE(date_inscription) = ?");
        $stmt->execute([$today]);
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Gérer les inscriptions (formulaire)
     */
    public function inscriptions() {
        if (isset($_GET['delete'])) {
            $this->deleteInscription($_GET['delete']);
            header('Location: index.php?page=admin_inscriptions');
            exit;
        }

        if (isset($_GET['edit'])) {
            $inscription = $this->getInscriptionById($_GET['edit']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateInscription($_GET['edit'], $_POST);
                header('Location: index.php?page=admin_dashboard#inscriptions');
                exit;
            }
            
            $events = $this->getAllEvents();
            require __DIR__ . '/../View/BackOffice/admin_inscriptions_form.php';
            exit;
        }

        if (isset($_GET['add'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->createInscription($_POST);
                header('Location: index.php?page=admin_dashboard#inscriptions');
                exit;
            }
            
            $inscription = null;
            $events = $this->getAllEvents();
            require __DIR__ . '/../View/BackOffice/admin_inscriptions_form.php';
            exit;
        }

        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    // ========== CRUD PROPOSITIONS ==========
    
    /**
     * CREATE - Créer une proposition
     */
    private function createProposition($data) {
        $stmt = $this->pdo->prepare("INSERT INTO proposition (association_nom, email_contact, tel, type, description) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([
            $data['association_nom'],
            $data['email_contact'],
            $data['tel'],
            $data['type'],
            $data['description']
        ]);
    }

    /**
     * READ - Récupérer toutes les propositions
     */
    private function getAllPropositions() {
        $stmt = $this->pdo->query("SELECT * FROM proposition ORDER BY date_proposition DESC");
        $results = $stmt->fetchAll();
        
        $propositions = [];
        foreach ($results as $row) {
            $proposition = new Proposition();
            $proposition->hydrate($row);
            $propositions[] = $proposition;
        }
        return $propositions;
    }

    /**
     * READ - Récupérer une proposition par ID
     */
    private function getPropositionById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM proposition WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        
        if ($result) {
            $proposition = new Proposition();
            $proposition->hydrate($result);
            return $proposition;
        }
        return null;
    }

    /**
     * UPDATE - Mettre à jour une proposition
     */
    private function updateProposition($id, $data) {
        $stmt = $this->pdo->prepare("UPDATE proposition SET association_nom=?, email_contact=?, tel=?, type=?, description=? WHERE id=?");
        return $stmt->execute([
            $data['association_nom'],
            $data['email_contact'],
            $data['tel'],
            $data['type'],
            $data['description'],
            $id
        ]);
    }

    /**
     * DELETE - Supprimer une proposition
     */
    private function deleteProposition($id) {
        $stmt = $this->pdo->prepare("DELETE FROM proposition WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * COUNT - Compter les propositions
     */
    private function countPropositions() {
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM proposition");
        return (int) $stmt->fetch()['total'];
    }

    /**
     * Gérer les propositions (formulaire)
     */
    public function propositions() {
        if (isset($_GET['delete'])) {
            $this->deleteProposition($_GET['delete']);
            header('Location: index.php?page=admin_propositions');
            exit;
        }

        if (isset($_GET['edit'])) {
            $proposition = $this->getPropositionById($_GET['edit']);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateProposition($_GET['edit'], $_POST);
                header('Location: index.php?page=admin_dashboard#propositions');
                exit;
            }
            
            require __DIR__ . '/../View/BackOffice/admin_propositions_form.php';
            exit;
        }

        if (isset($_GET['add'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->createProposition($_POST);
                header('Location: index.php?page=admin_dashboard#propositions');
                exit;
            }
            
            $proposition = null;
            require __DIR__ . '/../View/BackOffice/admin_propositions_form.php';
            exit;
        }

        header('Location: index.php?page=admin_dashboard');
        exit;
    }
}
