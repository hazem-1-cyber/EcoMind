<?php
session_start();

$base = dirname(__DIR__, 2); // EcoMind/

require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';

$controller = new FormulaireController();
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Erreur : aucun résultat à afficher. <a href='formulaire.html'>Retour au formulaire</a>");
}

$reponse = $controller->getReponseById($id);

if (!$reponse) {
    die("Réponse introuvable (ID: $id). <a href='formulaire.html'>Retour</a>");
}

// Récupère les conseils IA (toujours générés automatiquement)
$conseils = $controller->getConseilsIA($id);

// Si pas en session, les générer maintenant
if (!$conseils) {
    $conseils = $controller->genererConseilsIA($id);
}

// Préparer les textes des conseils avec valeurs par défaut
$texteEau = isset($conseils['eau']) && $conseils['eau'] ? $conseils['eau']->getTexteConseil() : "Prenez des douches plus courtes !";
$texteEnergie = isset($conseils['energie']) && $conseils['energie'] ? $conseils['energie']->getTexteConseil() : "Baissez le chauffage d'1°C";
$texteTransport = isset($conseils['transport']) && $conseils['transport'] ? $conseils['transport']->getTexteConseil() : "Prenez le vélo ou les transports en commun";

// Tous les conseils sont générés par IA
$estConseilIA = true;
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
            <p class="subtitle">
                <?php if ($estConseilIA): ?>
                    Voici vos 3 conseils générés par IA et adaptés à votre profil unique
                <?php else: ?>
                    Voici vos 3 conseils sur mesure pour réduire votre empreinte carbone
                <?php endif; ?>
            </p>
        </header>

        <!-- BARRE DE PROGRÈS -->
        <div class="progress-section">
            <h2>Votre Progrès Écologique</h2>
            <div class="progress-bar-container">
                <div class="progress-bar" id="progressBar">
                    <span class="progress-text" id="progressText">0%</span>
                </div>
            </div>
            <p class="progress-info" id="progressInfo">Cliquez sur "J'ai appliqué ce conseil" pour chaque conseil que vous mettez en pratique !</p>
        </div>

        <!-- LES 3 CONSEILS PERSONNALISÉS -->
        <div class="tips-section">
            <h2>Vos 3 conseils personnalisés</h2>
            <?php if ($estConseilIA): ?>
            <div style="text-align: center; margin-bottom: 20px;">
                <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 25px; border-radius: 25px; font-size: 0.95em; display: inline-block; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                    🤖 Conseils générés par Intelligence Artificielle
                </span>
            </div>
            <?php endif; ?>

            <div class="tip-card">
                <div class="tip-icon">🚿</div>
                <h3>EAU</h3>
                <p><?= htmlspecialchars($texteEau) ?></p>
                <button class="tip-button" onclick="updateProgress(33, this)">J'ai appliqué ce conseil</button>
            </div>

            <div class="tip-card">
                <div class="tip-icon">🔥</div>
                <h3>ÉNERGIE</h3>
                <p><?= htmlspecialchars($texteEnergie) ?></p>
                <button class="tip-button" onclick="updateProgress(33, this)">J'ai appliqué ce conseil</button>
            </div>

            <div class="tip-card">
                <div class="tip-icon">🚗</div>
                <h3>TRANSPORT</h3>
                <p><?= htmlspecialchars($texteTransport) ?></p>
                <button class="tip-button" onclick="updateProgress(34, this)">J'ai appliqué ce conseil</button>
            </div>
        </div>

        <!-- SECTION PARTAGE -->
        <div class="share-section" style="text-align:center;margin:40px 0;padding:30px;background:#f0f8f0;border-radius:15px;">
            <h2 style="color:#013220;margin-bottom:20px;">🌍 Partagez votre engagement écologique !</h2>
            <p style="color:#666;margin-bottom:25px;">Inspirez vos amis à réduire leur empreinte carbone</p>
            
            <div class="share-buttons" style="display:flex;justify-content:center;gap:15px;flex-wrap:wrap;margin-bottom:20px;">
                <button onclick="partagerFacebook()" style="background:#1877f2;color:white;padding:12px 25px;border:none;border-radius:25px;font-size:16px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">📘</span> Facebook
                </button>
                
                <button onclick="partagerTwitter()" style="background:#1da1f2;color:white;padding:12px 25px;border:none;border-radius:25px;font-size:16px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">🐦</span> Twitter
                </button>
                
                <button onclick="partagerWhatsApp()" style="background:#25d366;color:white;padding:12px 25px;border:none;border-radius:25px;font-size:16px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">💬</span> WhatsApp
                </button>
                
                <button onclick="partagerLinkedIn()" style="background:#0077b5;color:white;padding:12px 25px;border:none;border-radius:25px;font-size:16px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">💼</span> LinkedIn
                </button>
                
                <button onclick="partagerEmail()" style="background:#ea4335;color:white;padding:12px 25px;border:none;border-radius:25px;font-size:16px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">📧</span> Email
                </button>
                
                <button onclick="copierLien()" style="background:#666;color:white;padding:12px 25px;border:none;border-radius:25px;font-size:16px;cursor:pointer;display:flex;align-items:center;gap:8px;">
                    <span style="font-size:20px;">🔗</span> Copier le lien
                </button>
            </div>
            
            <div id="copieMessage" style="display:none;color:#013220;font-weight:bold;margin-top:10px;">
                ✅ Lien copié dans le presse-papier !
            </div>
        </div>

        <p style="text-align:center;margin:60px 0;">
            <a href="generer_pdf.php?id=<?= $id ?>" target="_blank" style="background:#d4af37;color:#013220;padding:18px 50px;border-radius:50px;text-decoration:none;font-size:20px;font-weight:bold;margin-right:20px;display:inline-block;">
                📄 Télécharger en PDF
            </a>
            <a href="formulaire.html" style="background:#013220;color:white;padding:18px 50px;border-radius:50px;text-decoration:none;font-size:20px;font-weight:bold;display:inline-block;">
                Refaire le test
            </a>
        </p>

        <footer>
            <p>Chaque geste compte pour la planète 🌍</p>
        </footer>
    </div>

    <script src="script.js"></script>
    <script>
        // URL actuelle pour le partage
        const urlPartage = window.location.href;
        const urlFormulaire = window.location.origin + window.location.pathname.replace('resultats.php', 'formulaire.html');
        const messagePartage = "🌱 Je viens de calculer mon empreinte écologique avec EcoMind et j'ai reçu des conseils personnalisés pour la réduire ! Faites le test vous aussi : ";

        function partagerFacebook() {
            const url = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(urlFormulaire)}&quote=${encodeURIComponent(messagePartage)}`;
            window.open(url, '_blank', 'width=600,height=400');
        }

        function partagerTwitter() {
            const texte = messagePartage + urlFormulaire;
            const url = `https://twitter.com/intent/tweet?text=${encodeURIComponent(texte)}&hashtags=EcoMind,EmpreinteCarbone,Écologie`;
            window.open(url, '_blank', 'width=600,height=400');
        }

        function partagerWhatsApp() {
            const texte = messagePartage + urlFormulaire;
            const url = `https://wa.me/?text=${encodeURIComponent(texte)}`;
            window.open(url, '_blank');
        }

        function partagerLinkedIn() {
            const url = `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(urlFormulaire)}`;
            window.open(url, '_blank', 'width=600,height=400');
        }

        function partagerEmail() {
            const sujet = "🌱 Découvre EcoMind - Calcule ton empreinte écologique";
            const corps = `Salut !\n\nJe viens de découvrir EcoMind, une application qui permet de calculer son empreinte écologique et de recevoir des conseils personnalisés pour la réduire.\n\nC'est rapide, gratuit et vraiment utile ! Je te recommande d'essayer :\n\n${urlFormulaire}\n\nEnsemble, faisons un geste pour la planète ! 🌍\n`;
            
            window.location.href = `mailto:?subject=${encodeURIComponent(sujet)}&body=${encodeURIComponent(corps)}`;
        }

        function copierLien() {
            navigator.clipboard.writeText(urlFormulaire).then(() => {
                const message = document.getElementById('copieMessage');
                message.style.display = 'block';
                setTimeout(() => {
                    message.style.display = 'none';
                }, 3000);
            }).catch(err => {
                alert('Erreur lors de la copie : ' + err);
            });
        }
    </script>
</body>
</html>