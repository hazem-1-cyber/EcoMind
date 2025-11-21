<?php
require_once(__DIR__ . '/../../../models/Depot.php');
require_once(__DIR__ . '/../../../models/Stock.php');
require_once(__DIR__ . '/../../../models/Produit.php');

$depotModel = new Depot();
$stockModel = new Stock();
$produitModel = new Produit();

$depots = $depotModel->getAll();

echo "<h2>Stocks par dépôt</h2>";

foreach ($depots as $depot) {
    echo "<h3>" . htmlspecialchars($depot['region']) . "</h3><ul>";
    $stocks = $stockModel->getByDepot($depot['id']);
    if ($stocks) {
        foreach ($stocks as $s) {
            $produit = $produitModel->getById($s['produit_id']);
            echo "<li>" . htmlspecialchars($produit['nom']) . " : " . $s['quantite'] . "</li>";
        }
    } else {
        echo "<li>Aucun produit en stock.</li>";
    }
    echo "</ul>";
}
?>
<link rel="stylesheet" href="/../public/style.css">

