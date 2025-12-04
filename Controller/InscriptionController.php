<?php
// Controller/InscriptionController.php
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Core/Response.php';

class InscriptionController {
    private $request;
    private $inscriptionModel;
    private $evenementModel;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->inscriptionModel = new Inscription();
        $this->evenementModel = new Evenement();
    }

    /**
     * Display inscription form or handle submission
     */
    public function index() {
        $eventId = $this->request->get('id');
        
        if (!$eventId) {
            $response = new Response();
            $response->redirect('index.php?page=events');
            return;
        }
        
        // Handle form submission
        if ($this->request->isPost()) {
            $inscription = new InscriptionEntity();
            $inscription->setEvenementId($this->request->post('evenement_id'))
                       ->setNom($this->request->post('nom'))
                       ->setPrenom($this->request->post('prenom'))
                       ->setAge($this->request->post('age'))
                       ->setEmail($this->request->post('email'))
                       ->setTel($this->request->post('tel'));
            
            $this->inscriptionModel->create($inscription);
            
            $response = new Response();
            $response->redirect('index.php?page=events&msg=inscription_ok');
            return;
        }
        
        // Display form
        $event = $this->evenementModel->getById($eventId);
        
        if (!$event) {
            $response = new Response('Événement introuvable', 404);
            $response->send();
            return;
        }
        
        ob_start();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/inscription.php';
        require __DIR__ . '/../View/templates/footer.php';
        $content = ob_get_clean();
        
        $response = new Response($content);
        $response->send();
    }
}
