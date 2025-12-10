<?php
session_start();

// L'admin doit être connecté
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

require_once '../../config.php';
require_once '../../model/conseilReponse_model.php';
require_once '../../controller/reponseConseil_controller.php';
require_once 'notifications.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

// Récupération de la réponse
$db = Config::getConnexion();
$stmt = $db->prepare("SELECT * FROM reponse_formulaire WHERE idformulaire = ?");
$stmt->execute([$id]);
$reponse = $stmt->fetch(PDO::FETCH_OBJ);

if (!$reponse) {
    $_SESSION['error'] = "Réponse non trouvée (ID: $id)";
    header("Location: index.php");
    exit;
}

// TOUJOURS générer les conseils avec l'IA pour le backoffice
require_once '../../controller/ia_conseil_generator.php';
$iaGenerator = new IAConseilGenerator();

// Créer un objet ReponseFormulaire depuis les données de la base
$reponseObj = new ReponseFormulaire(
    (int)$reponse->idformulaire,
    $reponse->email,
    (int)$reponse->nb_personnes,
    (int)$reponse->douche_freq,
    (int)$reponse->douche_duree,
    $reponse->chauffage,
    (int)$reponse->temp_hiver,
    $reponse->transport_travail,
    (int)$reponse->distance_travail,
    $reponse->date_soumission
);

// Générer les conseils IA à chaque fois (pour avoir des variations)
$conseilsTextes = $iaGenerator->genererConseils($reponseObj);

// Préparer les conseils pour l'affichage
$conseils = [
    'eau' => $conseilsTextes['eau'] ?? 'Conseil eau non disponible',
    'energie' => $conseilsTextes['energie'] ?? 'Conseil énergie non disponible', 
    'transport' => $conseilsTextes['transport'] ?? 'Conseil transport non disponible'
];

// Générer les notifications pour cette page
$notifications = genererNotificationsPage($db, 'voir_conseil');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conseils Admin - Réponse #<?= $id ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/backoffice.css?v=<?php echo time(); ?>">
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
            <a href="test_ia.php" class="nav-item">
                <i class="fas fa-flask"></i>
                <span>Test IA</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-header">
            <h1><i class="fas fa-eye"></i> Conseils générés - Réponse #<?= $id ?></h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <!-- Cloche de notifications -->
                <?= renderNotificationBell($notifications) ?>
                <a href="index.php" class="logout-btn">Retour au Dashboard</a>
            </div>
        </div>

        <!-- INFORMATIONS RÉPONSE -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-envelope"></i></div>
                <div>
                    <p>Email</p>
                    <h3 style="font-size:16px;"><?= htmlspecialchars($reponse->email ?? '—') ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div>
                    <p>Personnes</p>
                    <h3><?= $reponse->nb_personnes ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-shower"></i></div>
                <div>
                    <p>Douches/sem</p>
                    <h3><?= $reponse->douche_freq ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-fire"></i></div>
                <div>
                    <p>Chauffage</p>
                    <h3 style="font-size:18px;"><?= ucfirst($reponse->chauffage ?? '—') ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-car"></i></div>
                <div>
                    <p>Transport</p>
                    <h3 style="font-size:16px;"><?= htmlspecialchars($reponse->transport_travail ?? '—') ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar"></i></div>
                <div>
                    <p>Date</p>
                    <h3 style="font-size:14px;"><?= date('d/m/Y H:i', strtotime($reponse->date_soumission)) ?></h3>
                </div>
            </div>
        </div>

        <!-- CONSEILS ATTRIBUÉS -->
        <div class="section">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <h2><i class="fas fa-lightbulb"></i> Conseils générés par IA</h2>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 20px; border-radius: 20px; font-size: 0.9em; font-weight: bold;">
                        🤖 IA Activée
                    </span>
                    <button onclick="window.location.href='voir_conseil.php?id=<?= $id ?>&regenerate=' + Date.now()" 
                            style="background: #667eea; color: white; padding: 8px 15px; border: none; border-radius: 15px; cursor: pointer; font-size: 0.85em; transition: all 0.3s;"
                            onmouseover="this.style.background='#5a6fd8'" 
                            onmouseout="this.style.background='#667eea'"
                            title="Génère de nouveaux conseils avec des variations">
                        🔄 Régénérer
                    </button>
                </div>
            </div>
            <div style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05)); padding: 15px; border-radius: 10px; margin-bottom: 20px; border-left: 4px solid #667eea;">
                <p style="margin: 0; color: #333; font-size: 0.95em;">
                    <strong>💡 Info :</strong> Ces conseils sont générés en temps réel par l'IA basée sur le profil de cet utilisateur. 
                    Cliquez sur "Régénérer" pour voir des variations.
                </p>
            </div>
            
            <div class="conseils-grid">
                <div class="conseil-card">
                    <div class="conseil-header">
                        <span class="conseil-badge conseil-badge-eau">💧 EAU</span>
                    </div>
                    <div class="conseil-body">
                        <p><?= nl2br(htmlspecialchars($conseils['eau'])) ?></p>
                    </div>
                </div>

                <div class="conseil-card">
                    <div class="conseil-header">
                        <span class="conseil-badge conseil-badge-energie">⚡ ÉNERGIE</span>
                    </div>
                    <div class="conseil-body">
                        <p><?= nl2br(htmlspecialchars($conseils['energie'])) ?></p>
                    </div>
                </div>

                <div class="conseil-card">
                    <div class="conseil-header">
                        <span class="conseil-badge conseil-badge-transport">🚗 TRANSPORT</span>
                    </div>
                    <div class="conseil-body">
                        <p><?= nl2br(htmlspecialchars($conseils['transport'])) ?></p>
                    </div>
                </div>
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