<?php
require_once(__DIR__ . '/../../models/Produit.php');
require_once(__DIR__ . '/../../models/Depot.php');

$produitModel = new Produit();
$depotModel = new Depot();
$produits = $produitModel->getAll();
$depots = $depotModel->getAll();
$message = '';

// Traitement simple du formulaire d’achat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'] ?? '';
    $depot_id = $_POST['depot_id'] ?? '';
    $qte = intval($_POST['quantite'] ?? 0);
    if ($produit_id && $depot_id && $qte > 0) {
        $message = '<span class="success">Votre demande d\'achat a été enregistrée !</span>';
        // Ici, insère le vrai traitement achat/stock si nécessaire
    } else {
        $message = '<span class="error">Veuillez remplir tous les champs correctement.</span>';
    }
}
?>
<?php
require_once(__DIR__ . '/../../models/Produit.php');
require_once(__DIR__ . '/../../models/Depot.php');
require_once(__DIR__ . '/../../models/Stock.php');

$produitModel = new Produit();
$depotModel = new Depot();
$stockModel = new Stock();
$produits = $produitModel->getAll();
$depots = $depotModel->getAll();
$message = '';

// Traitement formulaire achat
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produit_id = $_POST['produit_id'] ?? '';
    $depot_id = $_POST['depot_id'] ?? '';
    $qte = intval($_POST['quantite'] ?? 0);
    if ($produit_id && $depot_id && $qte > 0) {
        $stock = $stockModel->getOne($depot_id, $produit_id);
        if ($stock) {
            if ($stock['quantite'] >= $qte) {
                $newStock = $stock['quantite'] - $qte;
                $stockModel->updateStock($depot_id, $produit_id, $newStock);
                $message = '<span class="success">Achat validé ! Nouveau stock dispo : ' . $newStock . '</span>';
            } else {
                $message = '<span class="error">Stock insuffisant dans ce dépôt (stock actuel : ' . $stock['quantite'] . ')</span>';
            }
        } else {
            $message = '<span class="error">Aucun stock trouvé pour ce produit dans ce dépôt.</span>';
        }
    } else {
        $message = '<span class="error">Veuillez remplir tous les champs correctement.</span>';
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Accueil - Catalogue | MonSite</title>
    <link rel="stylesheet" href="/depot-products/public/style.css">
</head>
<body>

<header>
    <h1>Bienvenue sur le catalogue en ligne</h1>
    <nav>
        <a href="#">Accueil</a>
        <a href="#">Panier</a>
        <a href="#">Contact</a>
    </nav>
</header>

<div class="dashboard">
    <h2>Nos produits</h2>
    <div class="product-grid">
    <?php if($produits): foreach($produits as $p): ?>
        <div class="product-card">
            <h3><?= htmlspecialchars($p['nom']) ?></h3>
            <p><?= htmlspecialchars($p['description']) ?></p>
            <span class="price">Prix: <?= htmlspecialchars($p['prix']) ?> €</span>
        </div>
    <?php endforeach; else: ?>
        <p>Aucun produit disponible.</p>
    <?php endif; ?>
    </div>
    
    <h2>Où trouver nos produits ?</h2>
    <ul>
    <?php if($depots): foreach($depots as $d): ?>
        <li><strong><?= htmlspecialchars($d['region']) ?></strong></li>
    <?php endforeach; else: ?>
        <li>Aucun dépôt trouvé.</li>
    <?php endif; ?>
    </ul>

    <h2>Acheter un produit</h2>
    <?php if($message) echo $message; ?>
    <form method="post" style="margin-bottom:45px;">
        <label>Produit :</label>
        <select name="produit_id" required>
            <option value="">Choisir...</option>
            <?php foreach($produits as $p): ?>
                <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Dépôt :</label>
        <select name="depot_id" required>
            <option value="">Choisir...</option>
            <?php foreach($depots as $d): ?>
                <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['region']) ?></option>
            <?php endforeach; ?>
        </select>
        <label>Quantité :</label>
        <input type="number" name="quantite" min="1" required>
        <input type="submit" value="Acheter" class="btn">
    </form>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> - MonSite</p>
</footer>
<script src="/depot-products/public/script.js"></script>
</body>
</html>