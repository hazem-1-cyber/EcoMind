<?php
require_once(__DIR__ . '/../models/Produit.php');
require_once(__DIR__ . '/../models/Depot.php');
require_once(__DIR__ . '/../models/Stock.php');

class ProduitController {
    public function list(): void {
        $produit = new Produit();
        $data = $produit->getAll();
        include __DIR__ . '/../views/backoffice/produits/list.php';
    }
public function add(): void {
    $error = null;
    $imagePath = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nom = $_POST['nom'] ?? '';
        $description = $_POST['description'] ?? '';
        $prix = $_POST['prix'] ?? '';
        // Gestion upload image
        if(isset($_FILES["image"]) && $_FILES["image"]["error"] == UPLOAD_ERR_OK){
            $tmp_name = $_FILES["image"]["tmp_name"];
            $filename = uniqid() . "_" . basename($_FILES["image"]["name"]);
            $imagePath = "public/images/" . $filename;
            move_uploaded_file($tmp_name, __DIR__ . "/../../" . $imagePath);
        }

        if (strlen(trim($nom)) < 2) {
            $error = 'Nom trop court';
        } elseif (!is_numeric($prix) || $prix <= 0) {
            $error = 'Prix invalide';
        } else {
            $produit = new Produit();
            if ($produit->create($nom, $description, $prix, $imagePath)) {
                header('Location: index.php?action=listProduitsAdmin');
                exit;
            } else {
                $error = "Erreur lors de l'ajout";
            }
        }
    }
    include __DIR__ . '/../views/backoffice/produits/add.php';
}

    public function edit(): void {
        $error = null;
        $produit = new Produit();
        $id = $_GET['id'] ?? '';
        $current = $produit->getById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $description = $_POST['description'] ?? '';
            $prix = $_POST['prix'] ?? '';
            if (strlen(trim($nom)) < 2) {
                $error = 'Nom trop court';
            } elseif (!is_numeric($prix) || $prix <= 0) {
                $error = 'Prix invalide';
            } else {
                if ($produit->update($id, $nom, $description, $prix)) {
                    header('Location: index.php?action=listProduitsAdmin');
                    exit;
                } else {
                    $error = "Erreur lors de la modification";
                }
            }
        }
        include __DIR__ . '/../views/backoffice/produits/edit.php';
    }
    public function delete(): void {
        $produit = new Produit();
        $id = $_GET['id'] ?? '';
        if ($id) $produit->delete($id);
        header('Location: index.php?action=listProduitsAdmin');
        exit;
    }
    public function buy(): void {
        $produitModel = new Produit();
        $depotModel = new Depot();
        $stockModel = new Stock();
        $produits = $produitModel->getAll();
        $depots = $depotModel->getAll();
        $message = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $produit_id = $_POST['produit_id'] ?? '';
            $depot_id = $_POST['depot_id'] ?? '';
            $quantite = intval($_POST['quantite'] ?? 0);
            if ($produit_id && $depot_id && $quantite > 0) {
                $stock = $stockModel->getOne($depot_id, $produit_id);
                if ($stock) {
                    $newQuantite = $stock['quantite'] + $quantite;
                    if ($stockModel->updateStock($depot_id, $produit_id, $newQuantite)) {
                        $message = "<span class='success'>Stock mis à jour !</span>";
                    } else {
                        $message = "<span class='error'>Erreur SQL.</span>";
                    }
                } else {
                    if ($stockModel->addStock($depot_id, $produit_id, $quantite)) {
                        $message = "<span class='success'>Produit ajouté au stock !</span>";
                    } else {
                        $message = "<span class='error'>Erreur SQL.</span>";
                    }
                }
            } else {
                $message = "<span class='error'>Remplis tous les champs.</span>";
            }
        }
        include __DIR__ . '/../views/backoffice/produits/buy.php';
    }
}
?>
