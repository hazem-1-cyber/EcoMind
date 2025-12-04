<?php
// Controller/AdminController.php
require_once __DIR__ . '/../Model/Evenement.php';
require_once __DIR__ . '/../Model/Inscription.php';
require_once __DIR__ . '/../Model/Proposition.php';
require_once __DIR__ . '/../Service/DashboardService.php';
require_once __DIR__ . '/../Core/View.php';
require_once __DIR__ . '/../Core/Response.php';

class AdminController {
    private $request;
    private $evenementModel;
    private $inscriptionModel;
    private $propositionModel;
    private $dashboardService;

    public function __construct(Request $request) {
        $this->request = $request;
        $this->evenementModel = new Evenement();
        $this->inscriptionModel = new Inscription();
        $this->propositionModel = new Proposition();
        $this->dashboardService = new DashboardService(
            $this->evenementModel,
            $this->inscriptionModel,
            $this->propositionModel
        );
    }

    /**
     * Display dashboard
     */
    public function dashboard() {
        // Handle delete actions
        if ($this->request->has('delete_event')) {
            $this->evenementModel->delete($this->request->get('delete_event'));
            $response = new Response();
            $response->redirect('index.php?page=admin_dashboard');
            return;
        }
        
        if ($this->request->has('delete_inscription')) {
            $this->inscriptionModel->delete($this->request->get('delete_inscription'));
            $response = new Response();
            $response->redirect('index.php?page=admin_dashboard');
            return;
        }
        
        if ($this->request->has('delete_proposition')) {
            $this->propositionModel->delete($this->request->get('delete_proposition'));
            $response = new Response();
            $response->redirect('index.php?page=admin_dashboard');
            return;
        }

        // Get dashboard data from service
        $data = $this->dashboardService->getDashboardData();
        
        // Render view
        $view = View::make(__DIR__ . '/../View/BackOffice/dashboard.php', $data);
        $content = $view->render();
        
        $response = new Response($content);
        $response->send();
    }

    /**
     * Manage events (add/edit)
     */
    public function events() {
        // Handle delete
        if ($this->request->has('delete')) {
            $this->evenementModel->delete($this->request->get('delete'));
            $response = new Response();
            $response->redirect('index.php?page=admin_events');
            return;
        }

        // Handle edit
        if ($this->request->has('edit')) {
            $event = $this->evenementModel->getById($this->request->get('edit'));
            
            if ($this->request->isPost()) {
                $event->setTitre($this->request->post('titre'))
                      ->setDescription($this->request->post('description'))
                      ->setType($this->request->post('type'))
                      ->setImageMain($this->request->post('image_main'))
                      ->setImageSecond($this->request->post('image_second'));
                
                $this->evenementModel->update($event);
                
                $response = new Response();
                $response->redirect('index.php?page=admin_dashboard#events');
                return;
            }
            
            $view = View::make(__DIR__ . '/../View/BackOffice/admin_events_form.php', ['event' => $event]);
            $content = $view->render();
            $response = new Response($content);
            $response->send();
            return;
        }

        // Handle add
        if ($this->request->has('add')) {
            if ($this->request->isPost()) {
                $event = new EvenementEntity();
                $event->setTitre($this->request->post('titre'))
                      ->setDescription($this->request->post('description'))
                      ->setType($this->request->post('type'))
                      ->setImageMain($this->request->post('image_main'))
                      ->setImageSecond($this->request->post('image_second'));
                
                $this->evenementModel->create($event);
                
                $response = new Response();
                $response->redirect('index.php?page=admin_dashboard#events');
                return;
            }
            
            $view = View::make(__DIR__ . '/../View/BackOffice/admin_events_form.php', ['event' => null]);
            $content = $view->render();
            $response = new Response($content);
            $response->send();
            return;
        }

        // Redirect to dashboard
        $response = new Response();
        $response->redirect('index.php?page=admin_dashboard');
    }

    /**
     * Manage inscriptions (add/edit)
     */
    public function inscriptions() {
        // Handle delete
        if ($this->request->has('delete')) {
            $this->inscriptionModel->delete($this->request->get('delete'));
            $response = new Response();
            $response->redirect('index.php?page=admin_inscriptions');
            return;
        }

        // Handle edit
        if ($this->request->has('edit')) {
            $inscription = $this->inscriptionModel->getById($this->request->get('edit'));
            
            if ($this->request->isPost()) {
                $inscription->setEvenementId($this->request->post('evenement_id'))
                           ->setNom($this->request->post('nom'))
                           ->setPrenom($this->request->post('prenom'))
                           ->setAge($this->request->post('age'))
                           ->setEmail($this->request->post('email'))
                           ->setTel($this->request->post('tel'));
                
                $this->inscriptionModel->update($inscription);
                
                $response = new Response();
                $response->redirect('index.php?page=admin_dashboard#inscriptions');
                return;
            }
            
            $events = $this->evenementModel->getAll();
            $view = View::make(__DIR__ . '/../View/BackOffice/admin_inscriptions_form.php', [
                'inscription' => $inscription,
                'events' => $events
            ]);
            $content = $view->render();
            $response = new Response($content);
            $response->send();
            return;
        }

        // Handle add
        if ($this->request->has('add')) {
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
                $response->redirect('index.php?page=admin_dashboard#inscriptions');
                return;
            }
            
            $events = $this->evenementModel->getAll();
            $view = View::make(__DIR__ . '/../View/BackOffice/admin_inscriptions_form.php', [
                'inscription' => null,
                'events' => $events
            ]);
            $content = $view->render();
            $response = new Response($content);
            $response->send();
            return;
        }

        // Redirect to dashboard
        $response = new Response();
        $response->redirect('index.php?page=admin_dashboard');
    }

    /**
     * Manage propositions (add/edit)
     */
    public function propositions() {
        // Handle delete
        if ($this->request->has('delete')) {
            $this->propositionModel->delete($this->request->get('delete'));
            $response = new Response();
            $response->redirect('index.php?page=admin_propositions');
            return;
        }

        // Handle edit
        if ($this->request->has('edit')) {
            $proposition = $this->propositionModel->getById($this->request->get('edit'));
            
            if ($this->request->isPost()) {
                $proposition->setAssociationNom($this->request->post('association_nom'))
                           ->setEmailContact($this->request->post('email_contact'))
                           ->setTel($this->request->post('tel'))
                           ->setType($this->request->post('type'))
                           ->setDescription($this->request->post('description'));
                
                $this->propositionModel->update($proposition);
                
                $response = new Response();
                $response->redirect('index.php?page=admin_dashboard#propositions');
                return;
            }
            
            $view = View::make(__DIR__ . '/../View/BackOffice/admin_propositions_form.php', ['proposition' => $proposition]);
            $content = $view->render();
            $response = new Response($content);
            $response->send();
            return;
        }

        // Handle add
        if ($this->request->has('add')) {
            if ($this->request->isPost()) {
                $proposition = new PropositionEntity();
                $proposition->setAssociationNom($this->request->post('association_nom'))
                           ->setEmailContact($this->request->post('email_contact'))
                           ->setTel($this->request->post('tel'))
                           ->setType($this->request->post('type'))
                           ->setDescription($this->request->post('description'));
                
                $this->propositionModel->create($proposition);
                
                $response = new Response();
                $response->redirect('index.php?page=admin_dashboard#propositions');
                return;
            }
            
            $view = View::make(__DIR__ . '/../View/BackOffice/admin_propositions_form.php', ['proposition' => null]);
            $content = $view->render();
            $response = new Response($content);
            $response->send();
            return;
        }

        // Redirect to dashboard
        $response = new Response();
        $response->redirect('index.php?page=admin_dashboard');
    }
}
