<?php
require_once(__DIR__ . '/../models/Depot.php');

class DepotController {
    public function list(): void {
        $depot = new Depot();
        $data = $depot->getAll();
        include __DIR__ . '/../views/backoffice/depots/list.php';
    }
    public function add(): void {
        $error = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $region = $_POST['region'] ?? '';
            if (strlen(string: trim($region)) < 2) {
                $error = 'Nom région trop court';
            } else {
                $depot = new Depot();
                if ($depot->create($region)) {
                    header(header: 'Location: index.php?action=listDepotsAdmin');
                    exit;
                } else {
                    $error = "Erreur lors de l'ajout";
                }
            }
        }
        include __DIR__ . '/../views/backoffice/depots/add.php';
    }
    public function edit(): void {
        $error = null;
        $depot = new Depot();
        $id = $_GET['id'] ?? '';
        $current = $depot->getById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $region = $_POST['region'] ?? '';
            if (strlen(string: trim(string: $region)) < 2) {
                $error = 'Nom région trop court';
            } else {
                if ($depot->update(id: $id, region: $region)) {
                    header(header: 'Location: index.php?action=listDepotsAdmin');
                    exit;
                } else {
                    $error = "Erreur lors de la modification";
                }
            }
        }
        include __DIR__ . '/../views/backoffice/depots/edit.php';
    }
    public function delete(): void {
        $depot = new Depot();
        $id = $_GET['id'] ?? '';
        if ($id) $depot->delete($id);
        header('Location: index.php?action=listDepotsAdmin');
        exit;
    }
}
?>
