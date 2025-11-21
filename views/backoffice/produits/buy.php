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
        // Check if stock exists for depot+product
        $stock = $stockModel->getOne($depot_id, $produit_id);
        if ($stock) {
            // Add to existing stock
            $newQuantite = $stock['quantite'] + $quantite;
            if ($stockModel->updateStock($depot_id, $produit_id, $newQuantite)) {
                $message = "<span class='success'>Stock mis à jour !</span>";
            } else {
                $message = "<span class='error'>Erreur lors de la mise à jour.</span>";
            }
        } else {
            // Insert new stock entry
            if ($stockModel->addStock($depot_id, $produit_id, $quantite)) {
                $message = "<span class='success'>Stock ajouté !</span>";
            } else {
                $message = "<span class='error'>Erreur lors de l'ajout.</span>";
            }
        }
    } else {
        $message = "<span class='error'>Veuillez remplir tous les champs correctement.</span>";
    }
}
?>
<?php include __DIR__ . '/../templates/header.php'; ?>

<h2>Ajouter un produit au stock d'un dépôt</h2>
<link rel="stylesheet" href="../public/style.css">
<script src="/public/script.js"></script>

<form method="post">
    <label>Produit :</label>
    <select name="produit_id" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($produits as $prod): ?>
            <option value="<?= $prod['id'] ?>"><?= htmlspecialchars($prod['nom']) ?></option>
        <?php endforeach; ?>
    </select>
    <label>Dépôt :</label>
    <select name="depot_id" required>
        <option value="">-- Sélectionner --</option>
        <?php foreach ($depots as $dep): ?>
            <option value="<?= $dep['id'] ?>"><?= htmlspecialchars($dep['region']) ?></option>
        <?php endforeach; ?>
    </select>
    <label>Quantité :</label>
    <input type="number" name="quantite" min="1" required>
    <input type="submit" value="Ajouter au stock">
</form>
<?php if ($message) echo "<div>$message</div>"; ?>
<?php include __DIR__ . '/../templates/footer.php'; ?>
