<?php
session_start();

$base = dirname(__DIR__, 2); // EcoMind/

require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';

$controller = new FormulaireController();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Erreur : aucun résultat à afficher.");
}

$reponse = $controller->getReponseById($id);

if (!$reponse) {
    die("Réponse introuvable (ID: $id). <a href='formulaire.html'>Retour</a>");
}

// Récupère les 3 conseils attribués à cette réponse
$conseils = $controller->getConseilsAttribues($id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vos résultats éco - EcoMind</title>
    <link rel="stylesheet" href="conseil.css">
    <link rel="stylesheet" href="formulaire.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>🌱 Vos résultats personnalisés</h1>
            <img src="assets/images/Screenshot_2025-11-16_152042-removebg-preview.png" alt="Logo Eco Mind" class="logo-eco">
            <p class="subtitle">Voici vos 3 conseils sur mesure pour réduire votre empreinte carbone</p>
        </header>

        <!-- BARRE DE PROGRÈS -->
        <div class="progress-section">
            <h2>Votre Progrès Écologique</h2>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar">
                    <span class="progress-text" id="progressText">66%</span>
                </div>
            </div>
            <p class="progress-info">Vous avez déjà appliqué 2 conseils sur 3 ! Continuez comme ça !</p>
        </div>

        <!-- LES 3 CONSEILS PERSONNALISÉS -->
        <div class="tips-section">
            <h2>Vos 3 conseils personnalisés</h2>

            <div class="tip-card">
                <div class="tip-icon">🚿</div>
                <h3>EAU</h3>
                <p><?= htmlspecialchars($conseils['eau']->getTexteConseil() ?? "Prenez des douches plus courtes !") ?></p>
                <button class="tip-button" onclick="updateProgress(33)">J'ai appliqué ce conseil</button>
            </div>

            <div class="tip-card">
                <div class="tip-icon">🔥</div>
                <h3>ÉNERGIE</h3>
                <p><?= htmlspecialchars($conseils['energie']->getTexteConseil() ?? "Baissez le chauffage d’1°C") ?></p>
                <button class="tip-button" onclick="updateProgress(33)">J'ai appliqué ce conseil</button>
            </div>

            <div class="tip-card">
                <div class="tip-icon">🚗</div>
                <h3>TRANSPORT</h3>
                <p><?= htmlspecialchars($conseils['transport']->getTexteConseil() ?? "Prenez le vélo ou les transports en commun") ?></p>
                <button class="tip-button" onclick="updateProgress(34)">J'ai appliqué ce conseil</button>
            </div>
        </div>

        <p style="text-align:center;margin:60px 0;">
            <a href="formulaire.html" style="background:#013220;color:white;padding:18px 50px;border-radius:50px;text-decoration:none;font-size:20px;font-weight:bold;">
                Refaire le test
            </a>
        </p>

        <footer>
            <p>Chaque geste compte pour la planète 🌍</p>
        </footer>
    </div>

    <script src="script.js"></script>
</body>
</html>