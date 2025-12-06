<?php
require_once 'config.php';

try {
    $db = Config::getConnexion();
    
    // Vérifier la structure de la table dons
    $stmt = $db->query("DESCRIBE dons");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Structure de la table 'dons':</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    
    $hasImageColumn = false;
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . $column['Field'] . "</td>";
        echo "<td>" . $column['Type'] . "</td>";
        echo "<td>" . $column['Null'] . "</td>";
        echo "<td>" . $column['Key'] . "</td>";
        echo "<td>" . $column['Default'] . "</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'image_path') {
            $hasImageColumn = true;
        }
    }
    echo "</table>";
    
    if ($hasImageColumn) {
        echo "<p style='color: green; font-weight: bold;'>✓ La colonne 'image_path' existe dans la table.</p>";
    } else {
        echo "<p style='color: red; font-weight: bold;'>✗ La colonne 'image_path' n'existe PAS dans la table.</p>";
        echo "<p>Exécutez le script SQL suivant dans phpMyAdmin:</p>";
        echo "<pre>ALTER TABLE `dons` ADD COLUMN `image_path` VARCHAR(255) NULL DEFAULT NULL AFTER `description_don`;</pre>";
    }
    
    // Vérifier les dons existants
    $stmt = $db->query("SELECT id, email, type_don, image_path FROM dons ORDER BY id DESC LIMIT 5");
    $dons = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>Derniers dons (5 plus récents):</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>ID</th><th>Email</th><th>Type</th><th>Image</th></tr>";
    
    foreach ($dons as $don) {
        echo "<tr>";
        echo "<td>" . $don['id'] . "</td>";
        echo "<td>" . htmlspecialchars($don['email']) . "</td>";
        echo "<td>" . $don['type_don'] . "</td>";
        echo "<td>" . ($don['image_path'] ? htmlspecialchars($don['image_path']) : '<em>Aucune</em>') . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>
