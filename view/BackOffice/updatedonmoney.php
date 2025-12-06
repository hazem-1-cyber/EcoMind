<?php
require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../model/DonModel.php";

$donCtrl = new DonController();
$message = '';
$error = '';

// Récupérer l'ID du don
$donId = $_GET['id'] ?? null;

if (!$donId) {
    header('Location: dons.php');
    exit;
}

// Récupérer les informations du don
$don = $donCtrl->getDonById($donId);

if (!$don || $don['type_don'] !== 'money') {
    header('Location: dons.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $montant = floatval($_POST['montant'] ?? 0);
    $email = trim($_POST['email'] ?? '');
    $statut = $_POST['statut'] ?? '';
    
    // Validation
    if ($montant < 10) {
        $error = "Le montant doit être d'au moins 10 TND";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Email invalide";
    } elseif (!in_array($statut, ['pending', 'validated', 'rejected', 'cancelled'])) {
        $error = "Statut invalide";
    } else {
        // Mettre à jour le don
        $donObj = new Don();
        $donObj->setId($donId);
        $donObj->setMontant($montant);
        $donObj->setEmail($email);
        $donObj->setStatut($statut);
        
        if ($donCtrl->updateDonMoney($donObj)) {
            $message = "Don modifié avec succès!";
            // Recharger les données
            $don = $donCtrl->getDonById($donId);
        } else {
            $error = "Erreur lors de la modification du don";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoMind - Modifier Don</title>
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
            <a href="dons.php" class="nav-item active">
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
            <div class="page-header-form">
                <a href="dons.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
                <h1><i class="fas fa-edit"></i> Modifier le don #<?= $don['id'] ?></h1>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST" class="modern-form">
                    <div class="form-group-modern">
                        <label><i class="fas fa-hashtag"></i> ID du don</label>
                        <input type="text" class="input-modern" value="<?= $don['id'] ?>" readonly>
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Non modifiable</span>
                    </div>

                    <div class="form-group-modern">
                        <label><i class="fas fa-calendar"></i> Date de création</label>
                        <input type="text" class="input-modern" value="<?= date('d/m/Y à H:i', strtotime($don['created_at'])) ?>" readonly>
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Non modifiable</span>
                    </div>

                    <div class="form-group-modern">
                        <label><i class="fas fa-envelope"></i> Email du donateur *</label>
                        <input 
                            type="email" 
                            name="email" 
                            class="input-modern" 
                            value="<?= htmlspecialchars($don['email']) ?>" 
                            required
                            placeholder="exemple@email.com"
                        >
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Email de contact du donateur</span>
                    </div>

                    <div class="form-group-modern">
                        <label><i class="fas fa-money-bill-wave"></i> Montant (TND) *</label>
                        <input 
                            type="number" 
                            name="montant" 
                            class="input-modern" 
                            value="<?= $don['montant'] ?>" 
                            min="10" 
                            step="0.01" 
                            required
                            placeholder="100.00"
                        >
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Montant minimum: 10 TND</span>
                    </div>

                    <div class="form-group-modern">
                        <label><i class="fas fa-flag"></i> Statut *</label>
                        <select name="statut" class="input-modern" required>
                            <option value="pending" <?= $don['statut'] === 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="validated" <?= $don['statut'] === 'validated' ? 'selected' : '' ?>>Validé</option>
                            <option value="rejected" <?= $don['statut'] === 'rejected' ? 'selected' : '' ?>>Rejeté</option>
                            <option value="cancelled" <?= $don['statut'] === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                        </select>
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Changez le statut du don</span>
                    </div>

                    <div class="form-group-modern">
                        <label><i class="fas fa-tag"></i> Type de don</label>
                        <input type="text" class="input-modern" value="Argent (Money)" readonly>
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Non modifiable</span>
                    </div>

                    <div class="form-group-modern">
                        <label><i class="fas fa-truck"></i> Mode de livraison</label>
                        <input type="text" class="input-modern" value="<?= ucfirst($don['livraison'] ?? 'Non spécifié') ?>" readonly>
                        <span class="field-hint"><i class="fas fa-info-circle"></i> Non modifiable</span>
                    </div>

                    <div class="form-actions-modern">
                        <button type="submit" class="btn-submit-modern">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                        <a href="dons.php" class="btn-cancel-modern">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
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
    // Validation en temps réel
    const montantInput = document.querySelector('input[name="montant"]');
    const emailInput = document.querySelector('input[name="email"]');

    montantInput.addEventListener('input', function() {
        if (parseFloat(this.value) < 10) {
            this.classList.add('error');
        } else {
            this.classList.remove('error');
        }
    });

    emailInput.addEventListener('input', function() {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(this.value)) {
            this.classList.add('error');
        } else {
            this.classList.remove('error');
        }
    });
</script>

</body>
</html>
