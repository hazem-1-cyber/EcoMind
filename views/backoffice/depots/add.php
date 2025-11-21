<?php include __DIR__ . '/../templates/header.php'; ?>
<h2>Ajouter un dépôt</h2>
<head>
<link rel="stylesheet" href="../../../../public/style.css">
<script src="../../../../public/script.js"></script>


</head>
<!--?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>-->
<form method="post">
    <label for="region">Région :</label>
    <input type="text" name="region" id="region" required>
    <input type="submit" value="Ajouter">
</form>
<?php include __DIR__ . '/../templates/footer.php'; ?>
