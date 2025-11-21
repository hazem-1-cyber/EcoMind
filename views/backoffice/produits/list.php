<?php
require_once(__DIR__ . '/../../../models/Produit.php');
$produitModel = new Produit();
$data = $produitModel->getAll();
?>

<h2>Liste des Produits</h2>
<link rel="stylesheet" href="../public/style.css">
<script src="/public/script.js"></script>

<a href="index.php?action=addProduit" class="btn">Ajouter un produit</a>
<div class="product-grid">
    
<?php
if ($data) {
    foreach ($data as $produit) {
        $img = $produit['image'] ? '/' . $produit['image'] : '/public/images/default_product.png';
        echo '<div class="product-card">';
        echo "<img src=\"$img\" alt=\"Photo\" class=\"product-img\">";
        echo "<h3>" . htmlspecialchars($produit['nom']) . "</h3>";
        echo "<p>" . htmlspecialchars($produit['description']) . "</p>";
        echo "<p class='price'>" . htmlspecialchars($produit['prix']) . " €</p>";
        echo "<a href=\"index.php?action=editProduit&id={$produit['id']}\" class=\"btn\">Éditer</a>";
        echo "<a href=\"index.php?action=deleteProduit&id={$produit['id']}\" class=\"btn\" onclick=\"return confirm('Supprimer ?');\">Supprimer</a>";
        echo "</div>";
    }
} else {
    echo "<p>Aucun produit trouvé.</p>";
}
?>
</div>

