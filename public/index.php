<?php
// Routage basique pour démo
require_once '../controllers/DepotController.php';
require_once '../controllers/ProduitController.php';

$action = $_GET['action'] ?? null;
switch ($action) {
    case 'addDepot':
        (new DepotController())->add();
        break;
    case 'editDepot':
        (new DepotController())->edit();
        break;
    case 'deleteDepot':
        (new DepotController())->delete();
        break;
    case 'listDepotsAdmin':
        (new DepotController())->list();
        break;

    case 'addProduit':
        (new ProduitController())->add();
        break;
    case 'editProduit':
        (new ProduitController())->edit();
        break;
    case 'deleteProduit':
        (new ProduitController())->delete();
        break;
    case 'listProduitsAdmin':
        (new ProduitController())->list();
        break;

    case 'buyProduitBackoffice':
        (new ProduitController())->buyBackoffice();
        break;
    case 'buyProduitFrontoffice':
        (new ProduitController())->buyFrontoffice();
        break;
    case 'listProduitsFrontoffice':
        (new ProduitController())->listFrontoffice();
        break;
    case 'showStockFrontoffice':
        (new DepotController())->showStockFrontoffice();
        break;

    default:
        include '../views/dashboard.php';
        break;
}
?>
