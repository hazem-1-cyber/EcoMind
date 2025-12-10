session_start();

$base = dirname(__DIR__, 2);
require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reponse_id'])) {
    $reponseId = (int)$_POST['reponse_id'];
    
    $controller = new FormulaireController();
    
    // Supprimer les anciens conseils de la session
    if (isset($_SESSION['conseils_ia_' . $reponseId])) {
        unset($_SESSION['conseils_ia_' . $reponseId]);
    }
    
    // Régénérer les conseils
    $controller->genererConseilsIA($reponseId);
    
    // Rediriger avec message de succès
    $_SESSION['message'] = 'Conseils régénérés avec succès !';
    header('Location: gestion_ia.php');
    exit;
} else {
    header('Location: gestion_ia.php');
    exit;
}
?>