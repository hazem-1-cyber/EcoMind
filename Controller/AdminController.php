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
                header('Location: index.php?page=admin_events');
                exit;
            }
            require __DIR__ . '/../View/templates/header.php';
            require __DIR__ . '/../View/templates/navbar.php';
            require __DIR__ . '/../View/BackOffice/admin_events_form.php';
            require __DIR__ . '/../View/templates/footer.php';
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
                header('Location: index.php?page=admin_events');
                exit;
            }
            $event = null;
            require __DIR__ . '/../View/templates/header.php';
            require __DIR__ . '/../View/templates/navbar.php';
            require __DIR__ . '/../View/BackOffice/admin_events_form.php';
            require __DIR__ . '/../View/templates/footer.php';
            exit;
        }

        $events = $this->evenementModel->getAll();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/BackOffice/admin_events.php';
        require __DIR__ . '/../View/templates/footer.php';
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
                header('Location: index.php?page=admin_inscriptions');
                exit;
            }
            $events = $this->evenementModel->getAll();
            require __DIR__ . '/../View/templates/header.php';
            require __DIR__ . '/../View/templates/navbar.php';
            require __DIR__ . '/../View/BackOffice/admin_inscriptions_form.php';
            require __DIR__ . '/../View/templates/footer.php';
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
                header('Location: index.php?page=admin_inscriptions');
                exit;
            }
            $inscription = null;
            $events = $this->evenementModel->getAll();
            require __DIR__ . '/../View/templates/header.php';
            require __DIR__ . '/../View/templates/navbar.php';
            require __DIR__ . '/../View/BackOffice/admin_inscriptions_form.php';
            require __DIR__ . '/../View/templates/footer.php';
            exit;
        }

        $inscriptions = $this->inscriptionModel->getAll();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/BackOffice/admin_inscriptions.php';
        require __DIR__ . '/../View/templates/footer.php';
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
                header('Location: index.php?page=admin_propositions');
                exit;
            }
            require __DIR__ . '/../View/templates/header.php';
            require __DIR__ . '/../View/templates/navbar.php';
            require __DIR__ . '/../View/BackOffice/admin_propositions_form.php';
            require __DIR__ . '/../View/templates/footer.php';
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
                header('Location: index.php?page=admin_propositions');
                exit;
            }
            $proposition = null;
            require __DIR__ . '/../View/templates/header.php';
            require __DIR__ . '/../View/templates/navbar.php';
            require __DIR__ . '/../View/BackOffice/admin_propositions_form.php';
            require __DIR__ . '/../View/templates/footer.php';
            exit;
        }

        $propositions = $this->propositionModel->getAll();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/BackOffice/admin_propositions.php';
        require __DIR__ . '/../View/templates/footer.php';
    }
}
