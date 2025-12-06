<?php
require_once 'config.php';

try {
    $db = Config::getConnexion();
    
    // Insérer un don de test avec une image fictive
    $sql = "INSERT INTO dons (type_don, email, association_id, statut, ville, cp, tel, description_don, image_path, created_at) 
            VALUES (:type_don, :email, :association_id, :statut, :ville, :cp, :tel, :description_don, :image_path, NOW())";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute([
        'type_don' => 'autre',
        'email' => 'test@example.com',
        'association_id' => 1,
        'statut' => 'pending',
        'ville' => 'Tunis',
        'cp' => '1000',
        'tel' => '12345678',
        'description_don' => 'Don de test avec image',
        'image_path' => 'uploads/dons/test_image.jpg' // Chemin fictif pour le test
    ]);
    
    if ($result) {
        $lastId = $db->lastInsertId();
        echo "<p style='color: green; font-weight: bold;'>✓ Don de test inséré avec succès ! ID: {$lastId}</p>";
        
        // Récupérer et afficher le don
        $stmt = $db->prepare("SELECT * FROM dons WHERE id = ?");
        $stmt->execute([$lastId]);
        $don = $stmt->fetch(PDO::FETCH_ASSOC);
        
        echo "<h3>Données du don inséré:</h3>";
        echo "<table border='1' cellpadding='5'>";
        foreach ($don as $key => $value) {
            echo "<tr><td><strong>{$key}</strong></td><td>" . htmlspecialchars($value ?? 'NULL') . "</td></tr>";
        }
        echo "</table>";
        
        echo "<p><a href='view/BackOffice/dons.php'>Voir dans le back-office</a></p>";
        echo "<p><a href='test_image_column.php'>Vérifier la structure de la table</a></p>";
    } else {
        echo "<p style='color: red;'>✗ Erreur lors de l'insertion</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur: " . $e->getMessage() . "</p>";
}
?>
