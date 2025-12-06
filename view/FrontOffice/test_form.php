<?php
// Test simple pour vérifier que le formulaire fonctionne
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "<h2>Test de soumission du formulaire</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>✓ Données POST reçues:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>Test de connexion BD:</h3>";
    include(__DIR__ . '/../../config.php');
    include(__DIR__ . '/../../model/DonModel.php');
    include(__DIR__ . '/../../controller/DonController.php');
    
    try {
        $don = new Don();
        $don->setTypeDon($_POST['type_don'] ?? 'money');
        $don->setEmail($_POST['email'] ?? 'test@test.com');
        $don->setAssociationId(intval($_POST['association_id'] ?? 1));
        $don->setStatut('pending');
        $don->setMontant(floatval($_POST['montant'] ?? 50));
        $don->setLivraison($_POST['livraison'] ?? 'en_ligne');
        
        $controller = new DonController();
        $controller->addDon($don);
        
        echo "<p style='color: green; font-weight: bold;'>✓ Don enregistré avec succès dans la base de données!</p>";
        echo "<a href='don.html'>Retour au formulaire</a> | ";
        echo "<a href='../../test_connexion.php'>Voir tous les dons</a>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erreur: " . $e->getMessage() . "</p>";
    }
    
} else {
    echo "<p>Aucune donnée POST. <a href='don.html'>Aller au formulaire</a></p>";
}
?>
