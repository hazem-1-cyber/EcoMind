<?php include __DIR__ . '/../templates/header.php'; ?>
<h2>Ajouter un produit</h2>
<script src="/public/script.js"></script>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" required>
    <label for="description">Description :</label>
    <input type="text" name="description" id="description">
    <label for="prix">Prix :</label>
    <input type="number" name="prix" id="prix" step="0.01" required>
    <label for="image">Image :</label>
    <input type="file" name="image" id="image" accept="image/*">
    <input type="submit" value="Ajouter">
</form>
<link rel="stylesheet" href="../public/style.css">


<?php include __DIR__ . '/../templates/footer.php'; ?>
