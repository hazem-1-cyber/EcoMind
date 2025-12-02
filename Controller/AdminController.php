<?php
// Controller/AdminController.php
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Proposition.php';

class AdminController {
    private $evenementModel;
    private $inscriptionModel;
    private $propositionModel;

    public function __construct() {
        $this->evenementModel = new Evenement();
        $this->inscriptionModel = new Inscription();
        $this->propositionModel = new Proposition();
    }

    public function dashboard() {
        // Handle deletes from dashboard
        if (isset($_GET['delete_event'])) {
            $this->evenementModel->delete($_GET['delete_event']);
            header('Location: index.php?page=admin_dashboard');
            exit;
        }
        if (isset($_GET['delete_inscription'])) {
            $this->inscriptionModel->delete($_GET['delete_inscription']);
            header('Location: index.php?page=admin_dashboard');
            exit;
        }
        if (isset($_GET['delete_proposition'])) {
            $this->propositionModel->delete($_GET['delete_proposition']);
            header('Location: index.php?page=admin_dashboard');
            exit;
        }
        
        $events = $this->evenementModel->getAll();
        $inscriptions = $this->inscriptionModel->getAll();
        $propositions = $this->propositionModel->getAll();
        
        require __DIR__ . '/../View/BackOffice/dashboard.php';
    }

    public function events() {
        // Handle delete
        if (isset($_GET['delete'])) {
            $this->evenementModel->delete($_GET['delete']);
            header('Location: index.php?page=admin_events');
            exit;
        }

        // Handle edit
        if (isset($_GET['edit'])) {
            $event = $this->evenementModel->getById($_GET['edit']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'titre' => $_POST['titre'],
                    'description' => $_POST['description'],
                    'type' => $_POST['type'],
                    'image_main' => $_POST['image_main'],
                    'image_second' => $_POST['image_second']
                ];
                $this->evenementModel->update($_GET['edit'], $data);
                header('Location: index.php?page=admin_dashboard#events');
                exit;
            }
            require __DIR__ . '/../View/BackOffice/admin_events_form.php';
            exit;
        }

        // Handle add
        if (isset($_GET['add'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'titre' => $_POST['titre'],
                    'description' => $_POST['description'],
                    'type' => $_POST['type'],
                    'image_main' => $_POST['image_main'],
                    'image_second' => $_POST['image_second']
                ];
                $this->evenementModel->create($data);
                header('Location: index.php?page=admin_dashboard#events');
                exit;
            }
            $event = null;
            require __DIR__ . '/../View/BackOffice/admin_events_form.php';
            exit;
        }

        // Redirect to dashboard
        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    public function inscriptions() {
        // Handle delete
        if (isset($_GET['delete'])) {
            $this->inscriptionModel->delete($_GET['delete']);
            header('Location: index.php?page=admin_inscriptions');
            exit;
        }

        // Handle edit
        if (isset($_GET['edit'])) {
            $inscription = $this->inscriptionModel->getById($_GET['edit']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'evenement_id' => $_POST['evenement_id'],
                    'nom' => $_POST['nom'],
                    'prenom' => $_POST['prenom'],
                    'age' => $_POST['age'],
                    'email' => $_POST['email'],
                    'tel' => $_POST['tel']
                ];
                $this->inscriptionModel->update($_GET['edit'], $data);
                header('Location: index.php?page=admin_dashboard#inscriptions');
                exit;
            }
            $events = $this->evenementModel->getAll();
            require __DIR__ . '/../View/BackOffice/admin_inscriptions_form.php';
            exit;
        }

        // Handle add
        if (isset($_GET['add'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'evenement_id' => $_POST['evenement_id'],
                    'nom' => $_POST['nom'],
                    'prenom' => $_POST['prenom'],
                    'age' => $_POST['age'],
                    'email' => $_POST['email'],
                    'tel' => $_POST['tel']
                ];
                $this->inscriptionModel->create($data);
                header('Location: index.php?page=admin_dashboard#inscriptions');
                exit;
            }
            $inscription = null;
            $events = $this->evenementModel->getAll();
            require __DIR__ . '/../View/BackOffice/admin_inscriptions_form.php';
            exit;
        }

        // Redirect to dashboard
        header('Location: index.php?page=admin_dashboard');
        exit;
    }

    public function propositions() {
        // Handle delete
        if (isset($_GET['delete'])) {
            $this->propositionModel->delete($_GET['delete']);
            header('Location: index.php?page=admin_propositions');
            exit;
        }

        // Handle edit
        if (isset($_GET['edit'])) {
            $proposition = $this->propositionModel->getById($_GET['edit']);
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'association_nom' => $_POST['association_nom'],
                    'email_contact' => $_POST['email_contact'],
                    'tel' => $_POST['tel'],
                    'type' => $_POST['type'],
                    'description' => $_POST['description']
                ];
                $this->propositionModel->update($_GET['edit'], $data);
                header('Location: index.php?page=admin_dashboard#propositions');
                exit;
            }
            require __DIR__ . '/../View/BackOffice/admin_propositions_form.php';
            exit;
        }

        // Handle add
        if (isset($_GET['add'])) {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $data = [
                    'association_nom' => $_POST['association_nom'],
                    'email_contact' => $_POST['email_contact'],
                    'tel' => $_POST['tel'],
                    'type' => $_POST['type'],
                    'description' => $_POST['description']
                ];
                $this->propositionModel->create($data);
                header('Location: index.php?page=admin_dashboard#propositions');
                exit;
            }
            $proposition = null;
            require __DIR__ . '/../View/BackOffice/admin_propositions_form.php';
            exit;
        }

        // Redirect to dashboard
        header('Location: index.php?page=admin_dashboard');
        exit;
    }
}
