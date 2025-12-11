<?php
// index.php - Point d'entrée MVC
session_start();

require_once 'config.php';

// Autoload des classes
spl_autoload_register(function($class){
    $paths = [
        'Controller/'.$class.'.php',
        'Model/'.$class.'.php'
    ];
    foreach($paths as $p) {
        if (file_exists($p)) {
            require_once $p;
            return;
        }
    }
});

// Récupérer la page demandée
$page = $_GET['page'] ?? 'events';
$id = $_GET['id'] ?? null;

// Router simple
switch ($page) {
    case 'events':
        $controller = new EvenementController();
        if ($id) {
            $controller->show($id);
        } else {
            $controller->index();
        }
        break;

    case 'proposer':
        $controller = new PropositionController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->submit();
        } else {
            $controller->form();
        }
        break;

    case 'inscription':
        $controller = new InscriptionController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->submit();
        } else {
            if (!$id) {
                echo "Événement introuvable.";
                exit;
            }
            $controller->form($id);
        }
        break;

    case 'admin':
    case 'admin_dashboard':
        $controller = new AdminController();
        $controller->dashboard();
        break;

    case 'admin_events':
        $controller = new AdminController();
        $controller->events();
        break;

    case 'admin_inscriptions':
        $controller = new AdminController();
        $controller->inscriptions();
        break;

    case 'admin_propositions':
        $controller = new AdminController();
        $controller->propositions();
        break;

    case 'statistiques':
        $controller = new StatistiquesController();
        $controller->index();
        break;

    case 'search_events':
        $controller = new AdminController();
        $controller->searchEvents();
        break;

    case 'search_inscriptions':
        $controller = new AdminController();
        $controller->searchInscriptions();
        break;

    case 'search_propositions':
        $controller = new AdminController();
        $controller->searchPropositions();
        break;

    default:
        // Page non trouvée - rediriger vers events
        header('Location: index.php?page=events');
        break;
}
