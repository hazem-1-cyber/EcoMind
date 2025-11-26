<?php
// Controller/InscriptionController.php
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Evenement.php';

class InscriptionController {
    private $model;
    private $evenementModel;

    public function __construct() {
        $this->model = new Inscription();
        $this->evenementModel = new Evenement();
    }

    public function form($eventId) {
        $event = $this->evenementModel->getById($eventId);
        if (!$event) {
            echo "Événement introuvable.";
            return;
        }
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/inscription.php';
        require __DIR__ . '/../View/templates/footer.php';
    }

    public function submit() {
        // traitement POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'evenement_id' => $_POST['evenement_id'] ?? null,
                'nom' => trim($_POST['nom'] ?? ''),
                'prenom' => trim($_POST['prenom'] ?? ''),
                'age' => intval($_POST['age'] ?? 0),
                'email' => trim($_POST['email'] ?? ''),
                'tel' => trim($_POST['tel'] ?? '')
            ];
            // (on suppose la validation JS a eu lieu côté client) -> serveur = vérif minimale
            if (empty($data['nom']) || empty($data['prenom']) || empty($data['email'])) {
                $_SESSION['error'] = "Veuillez remplir les champs obligatoires.";
                header("Location: ".$_SERVER['HTTP_REFERER']);
                exit;
            }
            $ok = $this->model->create($data);
            if ($ok) {
                // redirection vers page principale avec message (simplifié)
                header('Location: index.php?page=events&msg=inscription_ok');
                exit;
            } else {
                echo "Erreur lors de l'inscription.";
            }
        }
    }
}
