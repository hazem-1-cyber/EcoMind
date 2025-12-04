<?php
// index.php - Entry point with proper OOP MVC architecture
session_start();

require_once 'config.php';

// Autoload Core classes
spl_autoload_register(function($class){
    $paths = [
        'Core/'.$class.'.php',
        'Controller/'.$class.'.php',
        'Model/'.$class.'.php',
        'Entity/'.$class.'.php',
        'Service/'.$class.'.php'
    ];
    foreach($paths as $p) {
        if (file_exists($p)) {
            require_once $p;
            return;
        }
    }
});

// Create Request object
$request = new Request();

// Create Router
$router = new Router($request);

// Define routes
$router->any('events', 'EvenementController', 'index');
$router->any('event_detail', 'EvenementController', 'show');
$router->any('proposer', 'ProposerController', 'index');
$router->any('inscription', 'InscriptionController', 'index');

// Admin routes
$router->any('admin', 'AdminController', 'dashboard');
$router->any('admin_dashboard', 'AdminController', 'dashboard');
$router->any('admin_events', 'AdminController', 'events');
$router->any('admin_inscriptions', 'AdminController', 'inscriptions');
$router->any('admin_propositions', 'AdminController', 'propositions');

// Dispatch the request
$router->dispatch();
