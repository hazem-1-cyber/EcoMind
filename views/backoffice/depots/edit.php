<?php include __DIR__ . '/../templates/header.php'; ?>
<h2>Modifier le dépôt</h2>

<link rel="stylesheet" href="../public/style.css">
<script src="/public/script.js"></script>

<?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post">
    <label for="region">Région :</label>
    <input type="text" name="region" id="region" required value="<?php echo htmlspecialchars($current['region']); ?>">
    <input type="submit" value="Enregistrer">
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
