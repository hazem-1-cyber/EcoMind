<?php
// Controller/ProposerController.php
require_once __DIR__ . '/../Model/Proposition.php';
require_once __DIR__ . '/../Core/Response.php';

class ProposerController {
    private $request;
    private $propositionModel;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->propositionModel = new Proposition();
    }

    /**
     * Display proposition form or handle submission
     */
    public function index() {
        // Handle form submission
        if ($this->request->isPost()) {
            $proposition = new PropositionEntity();
            $proposition->setAssociationNom($this->request->post('association_nom', ''))
                       ->setEmailContact($this->request->post('email_contact', ''))
                       ->setTel($this->request->post('tel', ''))
                       ->setType($this->request->post('type', ''))
                       ->setDescription($this->request->post('description', ''));
            
            $this->propositionModel->create($proposition);
            
            $response = new Response();
            $response->redirect('index.php?page=events&msg=proposition_ok');
            return;
        }
        
        // Display form
        ob_start();
        require __DIR__ . '/../View/templates/header.php';
        require __DIR__ . '/../View/templates/navbar.php';
        require __DIR__ . '/../View/FrontOffice/proposer_event.php';
        require __DIR__ . '/../View/templates/footer.php';
        $content = ob_get_clean();
        
        $response = new Response($content);
        $response->send();
    }
}
