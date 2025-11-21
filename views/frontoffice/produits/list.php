<?php
require_once(__DIR__ . '/../../../models/Produit.php');
$produitModel = new Produit();
$data = $produitModel->getAll();
?>

<h2>Liste des produits disponibles</h2>
<script src="/public/script.js"></script>
<link rel="stylesheet" href="../../../../public/style.css">


<ul>
<?php
if ($data && count($data)) {
    foreach ($data as $produit) {
        echo "<li><strong>" . htmlspecialchars($produit['nom']) . "</strong> - " .
            htmlspecialchars($produit['description']) . " - Prix: " .
            htmlspecialchars($produit['prix']) . "</li>";
    }
} else {
    echo "<li>Aucun produit trouvé.</li>";
}
?>
</ul>
