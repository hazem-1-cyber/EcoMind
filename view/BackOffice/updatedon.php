<?php
require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../model/DonModel.php";

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: lisdon.php');
    exit;
}

$donCtrl = new DonController();
$id = (int)$_GET['id'];
$don = $donCtrl->getDon($id);

if (!$don) {
    header('Location: lisdon.php?msg=not_found');
    exit;
}

// Page de consultation uniquement (pas de modification)
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoMind - Consulter le don #<?= $don['id'] ?></title>
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
    <h1 class="page-title">Consulter le don #<?= $don['id'] ?></h1>
    <p class="page-subtitle">Détails du don</p>

    <div class="form-view">
      <div class="form-grid">
        
        <div class="form-group">
          <label>ID du don</label>
          <input type="text" value="<?= $don['id'] ?>" readonly>
        </div>

        <div class="form-group">
          <label>Date de création</label>
          <input type="text" value="<?= date('d/m/Y H:i', strtotime($don['created_at'])) ?>" readonly>
        </div>

        <div class="form-group">
          <label for="email">Email du donateur</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($don['email']) ?>" readonly>
        </div>

        <div class="form-group">
          <label for="type_don">Type de don</label>
          <input type="text" value="<?= ucfirst(str_replace('_', ' ', $don['type_don'])) ?>" readonly>
        </div>

        <?php if ($don['type_don'] === 'money' && $don['montant']): ?>
        <div class="form-group">
          <label>Montant</label>
          <input type="text" value="<?= number_format($don['montant'], 2) ?> TND" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['livraison']): ?>
        <div class="form-group">
          <label>Mode de livraison</label>
          <input type="text" value="<?= ucfirst(str_replace('_', ' ', $don['livraison'])) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['ville']): ?>
        <div class="form-group">
          <label>Ville</label>
          <input type="text" value="<?= htmlspecialchars($don['ville']) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['cp']): ?>
        <div class="form-group">
          <label>Code postal</label>
          <input type="text" value="<?= htmlspecialchars($don['cp']) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['adresse']): ?>
        <div class="form-group full">
          <label>Adresse</label>
          <input type="text" value="<?= htmlspecialchars($don['adresse']) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['localisation']): ?>
        <div class="form-group full">
          <label>Localisation</label>
          <input type="text" value="<?= htmlspecialchars($don['localisation']) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['tel']): ?>
        <div class="form-group">
          <label>Téléphone</label>
          <input type="text" value="<?= htmlspecialchars($don['tel']) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($don['description_don']): ?>
        <div class="form-group full">
          <label>Description</label>
          <textarea readonly rows="4"><?= htmlspecialchars($don['description_don']) ?></textarea>
        </div>
        <?php endif; ?>

        <div class="form-group">
          <label>Statut du don</label>
          <input type="text" value="<?php 
            switch($don['statut']) {
              case 'pending': echo 'En attente'; break;
              case 'validated': echo 'Validé'; break;
              case 'rejected': echo 'Rejeté'; break;
              case 'cancelled': echo 'Annulé'; break;
              default: echo $don['statut'];
            }
          ?>" readonly>
        </div>

      </div>

      <div class="form-actions">
        <a href="lisdon.php" class="back-link">
          <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
      </div>
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