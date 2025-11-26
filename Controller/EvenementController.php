<?php
// Controller/EvenementController.php
require_once __DIR__ . '/../Model/Evenement.php';

class EvenementController {
    private $model;

    public function __construct() {
        $this->model = new Evenement();
    }

    public function index() {
        $events = $this->model->getAll();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/events.php';
        require __DIR__ . '/../View/templates/footer.php';
    }

    public function show($id) {
        $event = $this->model->getById($id);
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
}
