<?php
// index.php
session_start();

require_once 'config.php';

// simple autoload controllers
spl_autoload_register(function($class){
    $paths = ['Controller/'.$class.'.php','Model/'.$class.'.php'];
    foreach($paths as $p) {
        if (file_exists($p)) require_once $p;
    }
});

$page = $_GET['page'] ?? 'events';
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

switch ($page) {
    case 'events':
        $c = new EvenementController();
        if ($id) $c->show($id);
        else $c->index();
        break;

    case 'proposer':
        // form and submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once 'Model/Proposition.php';
            $prop = new Proposition();
            $data = [
                'association_nom' => $_POST['association_nom'] ?? '',
                'email_contact' => $_POST['email_contact'] ?? '',
                'tel' => $_POST['tel'] ?? '',
                'type' => $_POST['type'] ?? '',
                'description' => $_POST['description'] ?? ''
            ];
            $prop->create($data);
            header('Location: index.php?page=events&msg=proposition_ok');
            exit;
        } else {
            require_once 'View/templates/header.php';
            require_once 'View/templates/navbar.php';
            require_once 'View/FrontOffice/proposer_event.php';
            require_once 'View/templates/footer.php';
        }
        break;

    case 'inscription':
        $ic = new InscriptionController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ic->submit();
        } else {
            if (!$id) { echo "Événement introuvable."; exit; }
            $ic->form($id);
        }
        break;

    case 'admin':
        // back-office minimal read-only views (connexion externe gérée ailleurs)
        $ac = new AdminController();
        $sub = $_GET['sub'] ?? 'events';
        if ($sub === 'events') $ac->events();
        elseif ($sub === 'inscriptions') $ac->inscriptions();
        elseif ($sub === 'propositions') $ac->propositions();
        else $ac->events();
        break;

    case 'admin_events':
        $ac = new AdminController();
        $ac->events();
        break;

    case 'admin_inscriptions':
        $ac = new AdminController();
        $ac->inscriptions();
        break;

    case 'admin_propositions':
        $ac = new AdminController();
        $ac->propositions();
        break;

    default:
        // page not found -> redirect to events
        header('Location: index.php?page=events');
        break;
}
