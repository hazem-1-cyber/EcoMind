<?php
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

$base = dirname(__DIR__, 2);
require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/reponseConseil_controller.php';
require_once $base . '/controller/ia_conseil_generator.php';
require_once 'notifications.php';

$controller = new FormulaireController();
$iaGenerator = new IAConseilGenerator();

// Récupérer toutes les réponses
$reponses = $controller->listReponses();

// Statistiques
$totalReponses = count($reponses);

// Vérifier la clé API OpenAI (elle est chargée via config.php)
$apiKey = getenv('OPENAI_API_KEY');
if (!$apiKey) {
    // Fallback : vérifier dans $_ENV et $_SERVER
    $apiKey = isset($_ENV['OPENAI_API_KEY']) ? $_ENV['OPENAI_API_KEY'] : 
              (isset($_SERVER['OPENAI_API_KEY']) ? $_SERVER['OPENAI_API_KEY'] : 'non configurée');
}

// Debug temporaire (à supprimer après test)
// echo "Debug API Key: '" . $apiKey . "' (longueur: " . strlen($apiKey) . ")<br>";

// Déterminer le mode IA
$modeIA = ($apiKey && $apiKey !== 'non configurée' && $apiKey !== 'votre_cle_api_ici' && strlen($apiKey) > 20) ? 'OpenAI GPT-3.5' : 'Mode Intelligent Par Défaut';

// === STATS ===
$db = Config::getConnexion();
$totalConseils   = $db->query("SELECT COUNT(*) FROM conseil")->fetchColumn();
$totalPersonnes  = $db->query("SELECT SUM(nb_personnes) FROM reponse_formulaire")->fetchColumn() ?: 0;

// Générer les notifications pour cette page
$notifications = genererNotificationsPage($db, 'ia');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion IA - EcoMind Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/backoffice.css?v=<?php echo time(); ?>">
    <style>
        .ia-badge {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
        }
        
        .feature-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .feature-card h3 {
            margin-bottom: 15px;
            color: #667eea;
        }
        
        .feature-card ul {
            line-height: 2;
            color: #333;
            list-style: none;
            padding: 0;
        }
        
        .feature-card ul li {
            padding-left: 25px;
            position: relative;
        }
        
        .feature-card ul li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #667eea;
            font-weight: bold;
        }
        
        .guide-step {
            display: flex;
            gap: 15px;
            align-items: start;
            margin-bottom: 20px;
        }
        
        .step-number {
            background: #667eea;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .action-btn {
            background: #667eea;
            color: white;
            padding: 12px 25px;
            border-radius: 25px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
            font-size: 1em;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../frontoffice/assets/images/Screenshot_2025-11-16_152042-removebg-preview.png" alt="EcoMind Logo">
                </div>
                <span class="logo-text">EcoMind</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="gerer_conseils.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Gestion de Conseils</span>
            </a>
            <a href="gestion_reponses.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Gestion des Réponses</span>
            </a>
            <a href="gestion_ia.php" class="nav-item active">
                <span>🤖 Gestion IA</span>
            </a>
            <a href="test_ia.php" class="nav-item">
                <i class="fas fa-flask"></i>
                <span>Test IA</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-header">
            <h1><i class="fas fa-robot"></i> Gestion de l'Intelligence Artificielle</h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <!-- Cloche de notifications -->
                <?= renderNotificationBell($notifications) ?>
                <a href="index.php" class="logout-btn">Retour au Dashboard</a>
            </div>
        </div>

        <!-- Configuration IA -->
        <div class="section">
            <h2>⚙️ Configuration IA</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-robot"></i></div>
                    <div>
                        <p>Mode actuel</p>
                        <h3 style="font-size: 14px;"><?= $modeIA ?></h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-key"></i></div>
                    <div>
                        <p>Clé API OpenAI</p>
                        <h3 style="font-size: 14px;">
                            <?php if ($apiKey && $apiKey !== 'non configurée' && $apiKey !== 'votre_cle_api_ici' && strlen($apiKey) > 20): ?>
                                ⚠️ Non configurée
                            <?php else: ?>
                                ✅ Configurée
                            <?php endif; ?>
                        </h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-chart-line"></i></div>
                    <div>
                        <p>Conseils générés</p>
                        <h3><?= $totalReponses * 3 ?></h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-users"></i></div>
                    <div>
                        <p>Utilisateurs</p>
                        <h3><?= $totalReponses ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Fonctionnalités IA -->
        <div class="section">
            <h2>🎯 Fonctionnalités de l'IA</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
                <div class="feature-card">
                    <h3>🎯 Personnalisation</h3>
                    <ul>
                        <li>Analyse de 9 paramètres utilisateur</li>
                        <li>15+ règles de personnalisation</li>
                        <li>Conseils adaptés au profil</li>
                        <li>Calculs basés sur des données réelles</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <h3>📊 Catégories</h3>
                    <ul>
                        <li>💧 EAU : Consommation et économies</li>
                        <li>🔥 ÉNERGIE : Chauffage et isolation</li>
                        <li>🚗 TRANSPORT : Mobilité durable</li>
                    </ul>
                </div>
                
                <div class="feature-card">
                    <h3>⚡ Performance</h3>
                    <ul>
                        <li>Génération instantanée</li>
                        <li>Mise en cache en session</li>
                        <li>Fallback automatique</li>
                        <li>Aucun ralentissement</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Guide d'utilisation -->
        <div class="section">
            <h2>📖 Comment ça marche ?</h2>
            <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05)); padding: 30px; border-radius: 15px; border-left: 4px solid #667eea;">
                <div class="guide-step">
                    <div class="step-number">1</div>
                    <div>
                        <strong style="color: #013220;">L'utilisateur remplit le formulaire</strong>
                        <p style="margin: 5px 0 0 0; color: #666;">Il renseigne ses habitudes : eau, énergie, transport</p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">2</div>
                    <div>
                        <strong style="color: #013220;">L'IA analyse automatiquement</strong>
                        <p style="margin: 5px 0 0 0; color: #666;">Le système évalue le profil et identifie les opportunités d'amélioration</p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">3</div>
                    <div>
                        <strong style="color: #013220;">Génération de 3 conseils personnalisés</strong>
                        <p style="margin: 5px 0 0 0; color: #666;">Chaque conseil est unique et adapté à la situation de l'utilisateur</p>
                    </div>
                </div>
                <div class="guide-step">
                    <div class="step-number">4</div>
                    <div>
                        <strong style="color: #013220;">Affichage et partage</strong>
                        <p style="margin: 5px 0 0 0; color: #666;">L'utilisateur voit ses conseils et peut les partager ou télécharger en PDF</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="section">
            <h2>🚀 Actions Rapides</h2>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <a href="../frontoffice/formulaire.html" target="_blank" class="action-btn">
                    📝 Tester le formulaire
                </a>
                <a href="index.php" class="action-btn" style="background: #013220;">
                    📊 Voir le dashboard
                </a>
                <a href="test_ia.php" class="action-btn" style="background: #764ba2;">
                    🧪 Tester l'IA
                </a>
            </div>
        </div>

        <?php if ($totalReponses === 0): ?>
        <div style="text-align: center; padding: 40px; color: #666; background: #f5f5f5; border-radius: 15px; margin-top: 30px;">
            <p style="font-size: 1.1em;">Aucune réponse pour le moment. Les statistiques IA apparaîtront dès qu'un utilisateur remplira le formulaire.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
<?= getNotificationJavaScript() ?>

// ==================== NOTIFICATIONS ====================

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'js-notification' + (type === 'error' ? ' error' : '');
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    notification.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Vérifier s'il y a une notification à afficher
<?php if (isset($_SESSION['notification'])): ?>
    showNotification('<?= addslashes($_SESSION['notification']) ?>', '<?= $_SESSION['notification_type'] ?? 'success' ?>');
    <?php 
    unset($_SESSION['notification']); 
    unset($_SESSION['notification_type']);
    ?>
<?php endif; ?>
</script>

</body>
</html>