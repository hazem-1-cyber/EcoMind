<?php
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

$base = dirname(__DIR__, 2);
require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/ia_conseil_generator.php';
require_once 'notifications.php';

// Créer une réponse de test
$reponseTest = new ReponseFormulaire(
    1,                      // id
    'test@example.com',     // email
    4,                      // nb personnes
    7,                      // douche freq
    15,                     // durée douche
    'electrique',           // chauffage
    22,                     // temp hiver
    'voiture',              // transport
    10                      // distance travail
);

// Vérifier la configuration des clés API
$openaiKey = getenv('OPENAI_API_KEY');
if (!$openaiKey) {
    $openaiKey = isset($_ENV['OPENAI_API_KEY']) ? $_ENV['OPENAI_API_KEY'] : 
                 (isset($_SERVER['OPENAI_API_KEY']) ? $_SERVER['OPENAI_API_KEY'] : null);
}

$huggingfaceKey = getenv('HUGGINGFACE_API_KEY');
if (!$huggingfaceKey) {
    $huggingfaceKey = isset($_ENV['HUGGINGFACE_API_KEY']) ? $_ENV['HUGGINGFACE_API_KEY'] : 
                      (isset($_SERVER['HUGGINGFACE_API_KEY']) ? $_SERVER['HUGGINGFACE_API_KEY'] : null);
}

// Statuts des clés API
$openaiConfigured = ($openaiKey && $openaiKey !== 'votre_cle_api_ici' && strlen($openaiKey) > 20);
$huggingfaceConfigured = ($huggingfaceKey && $huggingfaceKey !== 'votre_cle_huggingface_ici' && strlen($huggingfaceKey) > 10);

// Générer les conseils
$generator = new IAConseilGenerator();
$conseils = $generator->genererConseils($reponseTest);

// Utiliser les variables déjà définies plus haut
$apiKey = $openaiKey;
$hfKey = $huggingfaceKey;
$modeUtilise = $openaiConfigured ? 'OpenAI GPT-3.5' : 'Mode Intelligent Par Défaut';

// Générer les notifications pour cette page
$db = Config::getConnexion();
$notifications = genererNotificationsPage($db, 'test_ia');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test IA - EcoMind Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/backoffice.css?v=<?php echo time(); ?>">
    <style>
        .test-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .conseil-result {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        
        .conseil-result h3 {
            margin-top: 0;
            color: #667eea;
        }
        
        .conseil-result p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #333;
            margin: 10px 0 0 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }
        
        .status-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-warning {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .data-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .data-row:last-child {
            border-bottom: none;
        }
        
        .data-label {
            font-weight: bold;
            width: 200px;
            color: #666;
        }
        
        .data-value {
            color: #013220;
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
            <a href="gestion_ia.php" class="nav-item">
                <span>🤖 Gestion IA</span>
            </a>
            <a href="test_ia.php" class="nav-item active">
                <i class="fas fa-flask"></i>
                <span>Test IA</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-header">
            <h1><i class="fas fa-flask"></i> Test du Générateur IA</h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <!-- Cloche de notifications -->
                <?= renderNotificationBell($notifications) ?>
                <a href="gestion_ia.php" class="logout-btn">Retour Gestion IA</a>
            </div>
        </div>

        <!-- Configuration détectée -->
        <div class="section">
            <h2>🔧 Configuration Détectée</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-robot"></i></div>
                    <div>
                        <p>Mode utilisé</p>
                        <h3 style="font-size: 14px;"><?= $modeUtilise ?></h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-key"></i></div>
                    <div>
                        <p>OpenAI API Key</p>
                        <h3 style="font-size: 14px;">
                            <?php if ($openaiConfigured): ?>
                                <span class="status-badge status-warning ">❌ Non configurée</span>
                            <?php else: ?>
                                <span class="status-badge status-success">✅ Configurée</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-key"></i></div>
                    <div>
                        <p>Hugging Face API Key</p>
                        <h3 style="font-size: 14px;">
                            <?php if ($huggingfaceConfigured): ?>
                                <span class="status-badge status-success">✅ Configurée</span>
                            <?php else: ?>
                                <span class="status-badge status-warning">❌ Non configurée</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Données de test -->
        <div class="section">
            <h2>📋 Données de Test</h2>
            <div class="test-card">
                <div class="data-row">
                    <div class="data-label">Foyer :</div>
                    <div class="data-value"><?= $reponseTest->getNbPersonne() ?> personnes</div>
                </div>
                <div class="data-row">
                    <div class="data-label">Douches :</div>
                    <div class="data-value"><?= $reponseTest->getDoucheFreq() ?> fois/semaine de <?= $reponseTest->getDureeDouche() ?> minutes</div>
                </div>
                <div class="data-row">
                    <div class="data-label">Chauffage :</div>
                    <div class="data-value"><?= ucfirst($reponseTest->getChauffageType()) ?> à <?= $reponseTest->getTempHiver() ?>°C</div>
                </div>
                <div class="data-row">
                    <div class="data-label">Transport :</div>
                    <div class="data-value"><?= ucfirst($reponseTest->getTypeTransport()) ?> pour <?= $reponseTest->getDistTravail() ?> km</div>
                </div>
            </div>
        </div>

        <!-- Conseils générés -->
        <div class="section">
            <h2>✨ Conseils Générés par l'IA</h2>
            
            <div class="conseil-result" style="border-left-color: #1976d2;">
                <h3>💧 Conseil EAU</h3>
                <p><?= htmlspecialchars($conseils['eau']) ?></p>
            </div>

            <div class="conseil-result" style="border-left-color: #f57c00;">
                <h3>🔥 Conseil ÉNERGIE</h3>
                <p><?= htmlspecialchars($conseils['energie']) ?></p>
            </div>

            <div class="conseil-result" style="border-left-color: #388e3c;">
                <h3>🚗 Conseil TRANSPORT</h3>
                <p><?= htmlspecialchars($conseils['transport']) ?></p>
            </div>
        </div>

        <!-- Informations -->
        <div class="section">
            <h2>💡 Informations</h2>
            <div class="test-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05)); border-left: 4px solid #667eea;">
                <p style="margin: 0; line-height: 1.8; color: #333;">
                    <strong>Mode actuel :</strong> <?= $modeUtilise ?>
                    <br><br>
                    <?php if ($modeUtilise === 'Mode Intelligent Par Défaut'): ?>
                        <strong>💡 Note :</strong> Le mode par défaut utilise des règles intelligentes avec 4 variations par conseil. 
                        Chaque régénération produit des formulations différentes !
                        <br><br>
                        <strong>🎯 Pour encore plus :</strong> Activez OpenAI pour des conseils ultra-naturels et infiniment variés.
                    <?php else: ?>
                        <strong>✅ Excellent !</strong> Vous utilisez OpenAI pour générer des conseils ultra-personnalisés et naturels.
                        <br><br>
                        <strong>🔄 Variété maximale :</strong> Chaque régénération crée des conseils uniques !
                    <?php endif; ?>
                    <br><br>
                    <strong>🔄 Régénération :</strong> Cliquez sur "Régénérer les conseils" pour voir de nouvelles variations !
                </p>
            </div>
        </div>

        <!-- Actions -->
        <div class="section">
            <h2>🚀 Actions</h2>
            <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                <button onclick="window.location.href='test_ia.php?regenerate=' + Date.now()" class="btn btn-primary" style="background: #667eea; color: white; padding: 12px 25px; border-radius: 25px; border: none; cursor: pointer; font-size: 1em;" title="Génère de nouveaux conseils avec des variations">
                    🔄 Régénérer les conseils
                </button>
                <a href="../frontoffice/formulaire.html" target="_blank" class="btn btn-primary" style="background: #013220; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; display: inline-block;">
                    📝 Tester le formulaire complet
                </a>
                <a href="gestion_ia.php" class="btn btn-primary" style="background: #764ba2; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; display: inline-block;">
                    ⚙️ Retour Gestion IA
                </a>
            </div>
        </div>

        <!-- Résultat du test -->
        <div class="section">
            <div style="text-align: center; padding: 30px; background: #e8f5e9; border-radius: 15px; border: 2px solid #4caf50;">
                <h2 style="color: #2e7d32; margin: 0 0 10px 0;">✅ Test Réussi !</h2>
                <p style="color: #333; margin: 0; font-size: 1.1em;">
                    L'IA fonctionne correctement et génère des conseils personnalisés.
                </p>
            </div>
        </div>
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