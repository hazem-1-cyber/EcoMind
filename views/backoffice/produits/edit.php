<?php include __DIR__ . '/../templates/header.php'; ?>
<h2>Modifier le produit</h2>
<h2>Modifier le produit</h2>
<link rel="stylesheet" href="../public/style.css">
<script src="/public/script.js"></script>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required value="<?php echo htmlspecialchars($current['nom']); ?>">
    <label for="description">Description :</label>
    <input type="text" name="description" id="description" value="<?php echo htmlspecialchars($current['description']); ?>">
    <label for="prix">Prix :</label>
    <input type="number" name="prix" id="prix" step="0.01" required value="<?php echo htmlspecialchars($current['prix']); ?>">
    <label for="image">Image :</label>
    <input type="file" name="image" id="image" accept="image/*">
    <?php if($current['image']): ?>
       <img src="<?php echo '/' . $current['image']; ?>" style="max-width:120px;display:block;margin:12px 0;">
    <?php endif; ?>
    <input type="submit" value="Enregistrer">
</form>

<?php include __DIR__ . '/../templates/footer.php'; ?>
