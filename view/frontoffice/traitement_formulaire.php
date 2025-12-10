<?php
session_start();

$base = dirname(__DIR__, 2); // EcoMind/

require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';

try {
    $db = Config::getConnexion();
    
    // Insertion des données du formulaire
    $sql = "INSERT INTO reponse_formulaire 
            (email, nb_personnes, douche_freq, douche_duree, chauffage, temp_hiver, transport_travail, distance_travail, date_soumission)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $db->prepare($sql);
    $result = $stmt->execute(array(
        isset($_POST['email']) ? $_POST['email'] : '',
        (int)(isset($_POST['nb_personnes']) ? $_POST['nb_personnes'] : 1),
        (int)(isset($_POST['douche_freq']) ? $_POST['douche_freq'] : 0),
        (int)(isset($_POST['douche_duree']) ? $_POST['douche_duree'] : 0),
        isset($_POST['chauffage']) ? $_POST['chauffage'] : '',
        (int)(isset($_POST['temp_hiver']) ? $_POST['temp_hiver'] : 20),
        isset($_POST['transport_travail']) ? $_POST['transport_travail'] : '',
        (int)(isset($_POST['distance_travail']) ? $_POST['distance_travail'] : 0),
        date('Y-m-d H:i:s')
    ));
    
    if ($result) {
        $id = $db->lastInsertId();
        
        // Générer les conseils IA personnalisés
        $controller = new FormulaireController();
        $controller->genererConseilsIA($id);
        
        // Rediriger vers la page de résultats
        header("Location: resultats.php?id=" . $id);
        exit;
    } else {
        die("Erreur lors de l'insertion: " . implode(', ', $stmt->errorInfo()));
    }
    
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>