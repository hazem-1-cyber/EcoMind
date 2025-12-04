<?php
// Controller/EvenementController.php
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Core/Response.php';

class EvenementController {
    private $request;
    private $model;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->model = new Evenement();
    }

    /**
     * Display all events
     */
    public function index() {
        $events = $this->model->getAll();
        
        ob_start();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/events.php';
        require __DIR__ . '/../View/templates/footer.php';
        $content = ob_get_clean();
        
        $response = new Response($content);
        $response->send();
    }

    /**
     * Display single event detail
     */
    public function show() {
        $id = $this->request->get('id');
        
        if (!$id) {
            $response = new Response();
            $response->redirect('index.php?page=events');
            return;
        }
        
        $event = $this->model->getById($id);
        
        if (!$event) {
            $response = new Response('Événement introuvable', 404);
            $response->send();
            return;
        }
        
        ob_start();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/event_detail.php';
        require __DIR__ . '/../View/templates/footer.php';
        $content = ob_get_clean();
        
        $response = new Response($content);
        $response->send();
    }
}
