<?php
require_once __DIR__ . '/../../config.php';

// Charger les associations statiques depuis la base de données (consultation uniquement)
$db = Config::getConnexion();
$stmt = $db->query("SELECT * FROM associations ORDER BY nom ASC");
$associationsResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalAssociations = 0;
$associationsActives = 0;
$associations = [];

if ($associationsResult) {
    foreach ($associationsResult as $assoc) {
        $associations[] = [
            'id' => $assoc['id'],
            'nom' => $assoc['nom'],
            'description' => $assoc['description'] ?? 'Aucune description disponible',
            'email' => $assoc['email'] ?? 'contact@association.tn',
            'telephone' => $assoc['telephone'] ?? '+216 XX XXX XXX',
            'adresse' => $assoc['adresse'] ?? 'Tunisie',
            'date_partenariat' => $assoc['created_at'],
            'statut' => 'active'
        ];
    }
    $totalAssociations = count($associations);
    $associationsActives = $totalAssociations;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoMind - Associations Partenaires</title>
  <meta name="theme-color" content="#0B3D2E">
  <link rel="icon" href="images/favicon.svg" type="image/svg+xml">
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="style.css">
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
            <a href="listcategorie.php" class="nav-item">
                <i class="fas fa-tags"></i>
                <span>Catégories</span>
            </a>
            <a href="associations.php" class="nav-item active">
                <i class="fas fa-building"></i>
                <span>Associations</span>
            </a>
            <a href="statistiques.php" class="nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Statistiques</span>
            </a>
            <a href="parametres.php" class="nav-item">
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
            <h1 class="page-title">Associations Partenaires</h1>
            <p class="page-subtitle">Liste des organisations partenaires bénéficiaires des dons</p>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $totalAssociations ?></h3>
                        <p>Total Associations</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $associationsActives ?></h3>
                        <p>Associations Actives</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= date('Y') ?></h3>
                        <p>Année en cours</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <div class="stat-content">
                        <h3>100%</h3>
                        <p>Taux de partenariat</p>
                    </div>
                </div>
            </div>

            <!-- Associations Grid -->
            <div class="associations-grid">
                <?php foreach ($associations as $assoc): ?>
                    <div class="association-card">
                        <div class="association-header">
                            <div class="association-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <span class="statut-badge statut-validated"><?= ucfirst($assoc['statut']) ?></span>
                        </div>
                        
                        <h3 class="association-name"><?= htmlspecialchars($assoc['nom']) ?></h3>
                        <p class="association-description"><?= htmlspecialchars($assoc['description']) ?></p>
                        
                        <div class="association-details">
                            <div class="detail-item">
                                <i class="fas fa-envelope"></i>
                                <span><?= htmlspecialchars($assoc['email']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-phone"></i>
                                <span><?= htmlspecialchars($assoc['telephone']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <span><?= htmlspecialchars($assoc['adresse']) ?></span>
                            </div>
                            <div class="detail-item">
                                <i class="fas fa-calendar-check"></i>
                                <span>Depuis le <?= date('d/m/Y', strtotime($assoc['date_partenariat'])) ?></span>
                            </div>
                        </div>
                        
                        <div class="association-actions">
                            <button class="btn-action-assoc btn-view" title="Voir détails">
                                <i class="fas fa-eye"></i> Détails
                            </button>
                            <button class="btn-action-assoc btn-contact" title="Contacter">
                                <i class="fas fa-envelope"></i> Contact
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
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
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Notification dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const closeNotification = document.getElementById('closeNotification');
    
    if (notificationBtn && notificationDropdown && closeNotification) {
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
    }
</script>

</body>
</html>
