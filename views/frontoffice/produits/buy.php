<?php
require_once(__DIR__ . '/../../../models/Produit.php');
require_once(__DIR__ . '/../../../models/Depot.php');
require_once(__DIR__ . '/../../../models/Stock.php');

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
        // Cherche le stock pour ce produit+depot
        $stock = $stockModel->getOne($depot_id, $produit_id);
        if ($stock) {
            $currentStock = $stock['quantite'];
            if ($currentStock >= $quantite) {
                $newStock = $currentStock - $quantite;
                $stockModel->updateStock($depot_id, $produit_id, $newStock);
                $message = "Achat effectué ! Stock restant : $newStock.";
            } else {
                $message = "Stock insuffisant pour ce produit/dépôt !";
            }
        } else {
            // Pas de stock => ajout
            $stockModel->addStock($depot_id, $produit_id, 0); // produit créé avec 0 en stock
            $message = "Produit inexistant en stock : un nouvel enregistrement stock a été créé avec 0 quantité pour ce dépôt/produit.";
        }
    } else {
        $message = "Veuillez remplir tous les champs.";
    }
}
?>

<h2>Acheter un produit depuis le dépôt</h2>
<script src="/public/script.js"></script>

<form method="post">
    Produit :
    <select name="produit_id" required>
        <option value="">--Sélectionner--</option>
        <?php foreach ($produits as $prod): ?>
            <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nom']) ?></option>
        <?php endforeach; ?>
    </select>
    Dépôt :
    <select name="depot_id" required>
        <option value="">--Sélectionner--</option>
        <?php foreach ($depots as $dep): ?>
            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['region']) ?></option>
        <?php endforeach; ?>
    </select>
    Quantité :
    <input type="number" name="quantite" min="1" required>
    <input type="submit" value="Acheter">
</form>

