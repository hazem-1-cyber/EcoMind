<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

try {
    require_once __DIR__ . '/../../model/config/SettingsManager.php';
    require_once __DIR__ . '/../../config.php';
    $settingsManager = new SettingsManager();
} catch (Exception $e) {
    die("Erreur de chargement des paramètres: " . $e->getMessage() . "<br>Vérifiez que le dossier 'config' existe et contient SettingsManager.php et settings.json");
}

// Traiter la soumission du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldAutoValidate = $settingsManager->get('auto_validate_money', false);
    $newAutoValidate = isset($_POST['auto_validate_money']);
    
    $newSettings = [
        'notifications_enabled' => isset($_POST['notifications_enabled']),
        'email_notifications' => isset($_POST['email_notifications']),
        'auto_validate_money' => $newAutoValidate,
        'min_donation_amount' => (int) $_POST['min_donation_amount'],
        'objectif_mensuel' => (int) $_POST['objectif_mensuel'],
        'currency' => 'TND' // Toujours TND pour un site tunisien
    ];
    
    $settingsManager->setMultiple($newSettings);
    
    // Si la validation automatique vient d'être activée, valider tous les dons monétaires en attente
    if (!$oldAutoValidate && $newAutoValidate) {
        require_once __DIR__ . '/../../controller/DonController.php';
        $donController = new DonController();
        $db = Config::getConnexion();
        
        // Récupérer tous les dons monétaires en attente
        $sql = "SELECT id FROM dons WHERE type_don = 'money' AND statut = 'pending'";
        $stmt = $db->query($sql);
        $pendingDons = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Valider chaque don
        $validatedCount = 0;
        foreach ($pendingDons as $don) {
            if ($donController->acceptDon($don['id'])) {
                $validatedCount++;
            }
        }
        
        if ($validatedCount > 0) {
            $_SESSION['success_message'] = "Paramètres enregistrés avec succès ! $validatedCount don(s) monétaire(s) en attente ont été validés automatiquement.";
        } else {
            $_SESSION['success_message'] = 'Paramètres enregistrés avec succès !';
        }
    } else {
        $_SESSION['success_message'] = 'Paramètres enregistrés avec succès !';
    }
    
    header('Location: parametres.php');
    exit;
}

$settings = $settingsManager->getAll();
$successMessage = $_SESSION['success_message'] ?? null;
unset($_SESSION['success_message']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - Paramètres</title>
    <meta name="theme-color" content="#0B3D2E">
    <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
    <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <style>
        .settings-section {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 25px;
        }
        
        .settings-section h3 {
            color: #013220;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #A8E6CF;
            font-size: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 15px;
        }
        
        .form-group input[type="number"],
        .form-group select {
            width: 100%;
            max-width: 400px;
            padding: 12px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            background: white;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #A8E6CF;
            box-shadow: 0 0 0 3px rgba(168, 230, 207, 0.1);
        }
        
        .form-group small {
            display: block;
            color: #6c757d;
            margin-top: 5px;
            font-size: 13px;
        }
        
        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.4s;
            border-radius: 30px;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.4s;
            border-radius: 50%;
        }
        
        input:checked + .toggle-slider {
            background-color: #A8E6CF;
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(30px);
        }
        
        .setting-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid #f0f0f0;
            background: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .setting-item:last-child {
            margin-bottom: 0;
        }
        
        .setting-info h4 {
            margin: 0 0 5px 0;
            color: #2c3e50;
            font-size: 16px;
            font-weight: 600;
        }
        
        .setting-info p {
            margin: 0;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">
                    <img src="images/logo-ecomind.png" alt="EcoMind Logo" style="width: 50px; height: 50px; object-fit: contain;">
                </div>
                <div class="logo-text">EcoMind</div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="dons.php" class="nav-item">
                <i class="fas fa-hand-holding-heart"></i>
                <span>Tous les dons</span>
            </a>
            <a href="lisdon.php" class="nav-item">
                <i class="fas fa-list"></i>
                <span>Gestion des dons</span>
            </a>
            <a href="corbeille.php" class="nav-item">
                <i class="fas fa-trash-alt"></i>
                <span>Corbeille</span>
            </a>
            <a href="listcategorie.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Catégories</span>
            </a>
            <a href="associations.php" class="nav-item">
                <i class="fas fa-building"></i>
                <span>Associations</span>
            </a>
            <a href="statistiques.php" class="nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Statistiques</span>
            </a>
            <a href="parametres.php" class="nav-item active">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
            </a>
            <a href="#" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                <span>Déconnexion</span>
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Top Header -->
        <header class="top-header">
            <div class="header-left">
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
            
            <div class="header-right">
                <?php
                // Compter les dons en attente
                require_once __DIR__ . '/../../controller/DonController.php';
                $donCtrl = new DonController();
                $allDons = $donCtrl->listDons()->fetchAll();
                $donsPending = 0;
                foreach ($allDons as $don) {
                    if ($don['statut'] === 'pending') {
                        $donsPending++;
                    }
                }
                ?>
                <div class="header-icon notification-icon" id="notificationBtn" style="position: relative; cursor: pointer;" title="Dons en attente">
                    <i class="fas fa-bell"></i>
                    <?php if ($donsPending > 0): ?>
                        <span class="badge"><?= $donsPending ?></span>
                    <?php endif; ?>
                </div>
                <div class="user-profile" style="background: linear-gradient(135deg, #2c5f2d, #88b04b); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 50%; font-size: 24px; box-shadow: 0 2px 8px rgba(44, 95, 45, 0.3);">
                    🌱
                </div>
            </div>
        </header>

        <!-- Dropdown Notifications -->
        <div id="notificationDropdown" style="display: none; position: fixed; top: 70px; right: 20px; width: 350px; max-height: 500px; background: white; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); z-index: 10000; overflow: hidden; animation: slideDown 0.3s ease-out;">
            <div style="background: linear-gradient(135deg, #2c5f2d, #88b04b); color: white; padding: 20px; display: flex; align-items: center; justify-content: space-between;">
                <h3 style="margin: 0; font-size: 18px; display: flex; align-items: center; gap: 10px;">
                    <i class="fas fa-bell"></i>
                    Notifications
                </h3>
                <button id="closeNotification" style="background: rgba(255, 255, 255, 0.2); border: none; color: white; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 18px;">
                    ×
                </button>
            </div>
            
            <div style="padding: 15px; max-height: 400px; overflow-y: auto;">
                <?php if ($donsPending > 0): ?>
                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <i class="fas fa-exclamation-circle" style="color: #ff9800; font-size: 24px;"></i>
                            <div>
                                <strong style="color: #856404; font-size: 16px;"><?= $donsPending ?> don(s) en attente</strong>
                                <p style="margin: 5px 0 0 0; color: #856404; font-size: 14px;">Nécessitent votre attention</p>
                            </div>
                        </div>
                        <a href="lisdon.php" style="display: block; text-align: center; background: linear-gradient(135deg, #2c5f2d, #88b04b); color: white; padding: 10px; border-radius: 8px; text-decoration: none; font-weight: 600; margin-top: 10px;">
                            Gérer les dons
                        </a>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px 20px; color: #6c757d;">
                        <i class="fas fa-check-circle" style="font-size: 48px; color: #28a745; margin-bottom: 15px;"></i>
                        <p style="margin: 0; font-size: 16px; font-weight: 600;">Aucune notification</p>
                        <p style="margin: 5px 0 0 0; font-size: 14px;">Tous les dons sont traités !</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        </style>

        <div class="container">
            <h1 class="page-title">⚙️ Paramètres</h1>

            <?php if ($successMessage): ?>
                <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #28a745;">
                    <i class="fas fa-check-circle"></i> <?= $successMessage ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <!-- Configuration des Dons -->
                <div class="settings-section">
                    <h3><i class="fas fa-hand-holding-heart"></i> Configuration des dons</h3>
                    
                    <div class="form-group">
                        <label for="min_donation_amount">💰 Montant minimum de don (TND)</label>
                        <input type="number" id="min_donation_amount" name="min_donation_amount" value="<?= $settings['min_donation_amount'] ?>" min="1">
                        <small>Montant minimum accepté pour les dons monétaires en Dinar Tunisien</small>
                    </div>

                    <div class="form-group">
                        <label for="objectif_mensuel">🎯 Objectif mensuel de collecte (TND)</label>
                        <input type="number" id="objectif_mensuel" name="objectif_mensuel" value="<?= $settings['objectif_mensuel'] ?? 10000 ?>" min="100" step="100">
                        <small>Objectif de collecte mensuel affiché sur le dashboard</small>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>✅ Validation automatique des dons monétaires</h4>
                            <p>Valider automatiquement les dons après paiement réussi</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="auto_validate_money" <?= $settings['auto_validate_money'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Préférences de Notifications -->
                <div class="settings-section">
                    <h3><i class="fas fa-bell"></i> Préférences de notifications</h3>
                    
                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>🔔 Activer les notifications</h4>
                            <p>Recevoir des notifications pour les événements importants</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="notifications_enabled" <?= $settings['notifications_enabled'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>

                    <div class="setting-item">
                        <div class="setting-info">
                            <h4>📧 Notifications par email</h4>
                            <p>Recevoir un email pour chaque nouveau don</p>
                        </div>
                        <label class="toggle-switch">
                            <input type="checkbox" name="email_notifications" <?= $settings['email_notifications'] ? 'checked' : '' ?>>
                            <span class="toggle-slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Bouton de sauvegarde -->
                <div style="text-align: center; margin-top: 30px;">
                    <button type="submit" class="btn-primary" style="padding: 15px 40px; font-size: 16px;">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <footer>
            <p>© 2025 <strong>EcoMind</strong> • 
                <a href="#">Mentions légales</a> • 
                <a href="#">Support technique</a>
            </p>
        </footer>
    </main>
</div>

<script>
    // Toggle sidebar
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Notification dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const closeNotification = document.getElementById('closeNotification');
    
    notificationBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        notificationDropdown.style.display = notificationDropdown.style.display === 'none' ? 'block' : 'none';
    });
    
    closeNotification.addEventListener('click', () => {
        notificationDropdown.style.display = 'none';
    });
    
    document.addEventListener('click', (e) => {
        if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
            notificationDropdown.style.display = 'none';
        }
    });
</script>

</body>
</html>
