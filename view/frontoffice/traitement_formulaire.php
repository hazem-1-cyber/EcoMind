<?php
session_start();

$base = dirname(__DIR__, 2); // EcoMind/

require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';

try {
    $controller = new FormulaireController();

    $reponse = new ReponseFormulaire();
    $reponse
        ->setEmail($_POST['email'] ?? null)
        ->setNbPersonne((int)($_POST['nb_personnes'] ?? 1))
        ->setDoucheFreq((int)($_POST['douche_freq'] ?? 0))
        ->setDureeDouche((int)($_POST['douche_duree'] ?? 0))
        ->setChauffageType($_POST['chauffage'] ?? null)
        ->setTempHiver((int)($_POST['temp_hiver'] ?? 0))
        ->setTypeTransport($_POST['transport_travail'] ?? null)
        ->setDistTravail((int)($_POST['distance_travail'] ?? 0));

    $id = $controller->addReponse($reponse);

    if ($id > 0) {
        // Attribuer 3 conseils aléatoires à cette réponse
        $controller->attribuerConseils($id);
        
        // Rediriger vers la page de résultats
        header("Location: resultats.php?id=" . $id);
        exit;
    } else {
        die("ÉCHEC – l'insertion a raté");
    }
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>