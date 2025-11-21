<?php include __DIR__ . '/../templates/header.php'; ?>
<h2>Liste des Dépôts</h2>
<a href="index.php?action=addDepot" class="btn">Ajouter un dépôt</a>

<link rel="stylesheet" href="../public/style.css">
<script src="/public/script.js"></script>

<ul>
<?php
if ($data) {
    foreach ($data as $depot) {
        echo "<li><strong>" . htmlspecialchars($depot['region']) . "</strong> ";
        echo "<a href=\"index.php?action=editDepot&id={$depot['id']}\" class=\"btn\">Éditer</a>";
        echo "<a href=\"index.php?action=deleteDepot&id={$depot['id']}\" class=\"btn\" onclick=\"return confirm('Supprimer?');\">Supprimer</a>";
        echo "</li>";
    }
} else {
    echo "<li>Aucun dépôt trouvé.</li>";
}
?>
</ul>
<?php include __DIR__ . '/../templates/footer.php'; ?>
