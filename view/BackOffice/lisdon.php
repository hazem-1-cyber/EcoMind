<?php
require_once __DIR__ . "/../../controller/DonController.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDons()->fetchAll();

// Pagination - 10 dons par page
$itemsPerPage = 10;
$totalDons = count($dons);
$totalPages = ceil($totalDons / $itemsPerPage);

// Navigation intelligente depuis dashboard
$highlightDonId = isset($_GET['don_id']) ? (int)$_GET['don_id'] : null;
$currentPage = 1;

// Si un don_id est fourni, calculer la page automatiquement
if ($highlightDonId) {
    $donIndex = 0;
    foreach ($dons as $index => $don) {
        if ($don['id'] == $highlightDonId) {
            $donIndex = $index;
            break;
        }
    }
    $currentPage = floor($donIndex / $itemsPerPage) + 1;
} elseif (isset($_GET['page'])) {
    $currentPage = max(1, min((int)$_GET['page'], $totalPages));
}

// Extraire les dons pour la page actuelle
$offset = ($currentPage - 1) * $itemsPerPage;
$donsPage = array_slice($dons, $offset, $itemsPerPage);

// Calculer les statistiques
$totalCollecte = 0;
$donsValides = 0;

foreach ($dons as $don) {
    if ($don['type_don'] === 'money' && $don['montant']) {
        $totalCollecte += $don['montant'];
    }
    if ($don['statut'] === 'validated') {
        $donsValides++;
    }
}

$pourcentageValides = $totalDons > 0 ? round(($donsValides / $totalDons) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoMind - Gestion des dons</title>
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
            <a href="lisdon.php" class="nav-item active">
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
                $donsPending = 0;
                foreach ($dons as $don) {
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
                                <p style="margin: 5px 0 0 0; color: #856404; font-size: 14px;">Sur cette page</p>
                            </div>
                        </div>
                    </div>
                    
                    <?php
                    // Afficher les 5 derniers dons en attente
                    $recentPending = array_filter($dons, fn($d) => $d['statut'] === 'pending');
                    $recentPending = array_slice($recentPending, 0, 5);
                    foreach ($recentPending as $don):
                    ?>
                    <div style="border-bottom: 1px solid #e9ecef; padding: 12px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px;">
                            <span style="font-weight: 600; color: #2c5f2d; font-size: 14px;">
                                <?= ucfirst(str_replace('_', ' ', $don['type_don'])) ?>
                            </span>
                            <span style="font-size: 12px; color: #6c757d;">
                                <?= date('d/m/Y', strtotime($don['created_at'])) ?>
                            </span>
                        </div>
                        <div style="font-size: 13px; color: #495057;">
                            <?= htmlspecialchars(substr($don['email'], 0, 30)) ?><?= strlen($don['email']) > 30 ? '...' : '' ?>
                        </div>
                        <?php if ($don['type_don'] === 'money' && $don['montant']): ?>
                            <div style="font-weight: 700; color: #ff9800; margin-top: 5px;">
                                <?= number_format($don['montant'], 2) ?> TND
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
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
    <h1 class="page-title">Gestion des dons</h1>
    <p class="page-subtitle">Suivez et gérez tous les dons reçus pour les associations partenaires.</p>

    <!-- Statistiques -->
    <div class="stats-grid">
      <div class="stat-card">
        <div class="stat-value"><?= $totalDons ?></div>
        <div class="stat-label">Dons reçus</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= number_format($totalCollecte, 2) ?> TND</div>
        <div class="stat-label">Total collecté</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= $donsValides ?></div>
        <div class="stat-label">Dons validés</div>
      </div>
      <div class="stat-card">
        <div class="stat-value"><?= $pourcentageValides ?>%</div>
        <div class="stat-label">Taux de validation</div>
      </div>
    </div>

    <!-- Tableau des dons -->
    <div class="table-container">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Email</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Ville</th>
            <th>Image</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($donsPage)): ?>
            <tr>
              <td colspan="9" class="empty-message">
                Aucun don enregistré pour le moment.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($donsPage as $don): 
              $isHighlighted = ($highlightDonId && $don['id'] == $highlightDonId);
            ?>
              <tr <?= $isHighlighted ? 'id="highlighted-don" style="background: rgba(168, 230, 207, 0.2); animation: highlight-pulse 2s ease-in-out 3;"' : '' ?>>
                <td><?= $don['id'] ?></td>
                <td><?= date('d/m/Y', strtotime($don['created_at'])) ?></td>
                <td><?= htmlspecialchars($don['email']) ?></td>
                <td><?= ucfirst(str_replace('_', ' ', $don['type_don'])) ?></td>
                <td>
                  <?php if ($don['type_don'] === 'money' && $don['montant']): ?>
                    <?= number_format($don['montant'], 2) ?> TND
                  <?php else: ?>
                    -
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($don['ville'] ?? '-') ?></td>
                <td style="text-align: center;">
                  <?php if (!empty($don['image_don'])): ?>
                    <img src="../../<?= htmlspecialchars($don['image_don']) ?>" 
                         alt="Image du don" 
                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"
                         onclick="window.open('../../<?= htmlspecialchars($don['image_don']) ?>', '_blank')">
                  <?php else: ?>
                    <span style="color: #6c757d; font-size: 24px;">📷</span>
                  <?php endif; ?>
                </td>
                <td>
                  <?php
                  $statutClass = '';
                  $statutLabel = '';
                  switch($don['statut']) {
                    case 'pending':
                      $statutClass = 'statut-pending';
                      $statutLabel = 'En attente';
                      break;
                    case 'validated':
                      $statutClass = 'statut-validated';
                      $statutLabel = 'Validé';
                      break;
                    case 'rejected':
                      $statutClass = 'statut-rejected';
                      $statutLabel = 'Rejeté';
                      break;
                  }
                  ?>
                  <span class="statut-badge <?= $statutClass ?>"><?= $statutLabel ?></span>
                </td>
                <td class="actions">
                  <a href="updatedon.php?id=<?= $don['id'] ?>" class="btn-icon btn-edit" title="Consulter"><i class="fas fa-eye"></i></a>
                  
                  <?php if ($don['type_don'] === 'money'): ?>
                    <a href="updatedonmoney.php?id=<?= $don['id'] ?>" class="btn-icon btn-edit" title="Modifier montant" style="background: #ffc107;">
                      <i class="fas fa-dollar-sign"></i>
                    </a>
                  <?php endif; ?>
                  
                  <?php if ($don['statut'] === 'pending'): ?>
                    <a href="acceptdon.php?id=<?= $don['id'] ?>" class="btn-icon btn-validate" title="Accepter"><i class="fas fa-check"></i></a>
                    <a href="rejectdon.php?id=<?= $don['id'] ?>" class="btn-icon btn-delete" title="Rejeter"><i class="fas fa-times"></i></a>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-container">
      <div class="pagination">
        <?php if ($currentPage > 1): ?>
          <a href="?page=<?= $currentPage - 1 ?>" class="pagination-btn">
            <i class="fas fa-chevron-left"></i> Précédent
          </a>
        <?php endif; ?>
        
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
          <?php if ($i == 1 || $i == $totalPages || abs($i - $currentPage) <= 2): ?>
            <a href="?page=<?= $i ?>" class="pagination-number <?= $i == $currentPage ? 'active' : '' ?>">
              <?= $i ?>
            </a>
          <?php elseif (abs($i - $currentPage) == 3): ?>
            <span class="pagination-dots">...</span>
          <?php endif; ?>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
          <a href="?page=<?= $currentPage + 1 ?>" class="pagination-btn">
            Suivant <i class="fas fa-chevron-right"></i>
          </a>
        <?php endif; ?>
      </div>
      
      <div class="pagination-info">
        Affichage de <?= $offset + 1 ?> à <?= min($offset + $itemsPerPage, $totalDons) ?> sur <?= $totalDons ?> dons
      </div>
    </div>
    <?php endif; ?>
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

<style>
@keyframes highlight-pulse {
  0%, 100% { background: rgba(168, 230, 207, 0.2); }
  50% { background: rgba(168, 230, 207, 0.4); }
}

.pagination-container {
  margin-top: 30px;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 15px;
}

.pagination {
  display: flex;
  gap: 8px;
  align-items: center;
  flex-wrap: wrap;
  justify-content: center;
}

.pagination-btn, .pagination-number {
  padding: 10px 16px;
  border-radius: 8px;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s;
  border: 2px solid transparent;
}

.pagination-btn {
  background: linear-gradient(135deg, #2c5f2d, #88b04b);
  color: white;
  display: flex;
  align-items: center;
  gap: 8px;
}

.pagination-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(44, 95, 45, 0.3);
}

.pagination-number {
  background: white;
  color: #2c5f2d;
  border-color: #A8E6CF;
  min-width: 40px;
  text-align: center;
}

.pagination-number:hover {
  background: #f0fff4;
  border-color: #88b04b;
}

.pagination-number.active {
  background: linear-gradient(135deg, #2c5f2d, #88b04b);
  color: white;
  border-color: #2c5f2d;
}

.pagination-dots {
  padding: 0 8px;
  color: #88b04b;
  font-weight: bold;
}

.pagination-info {
  color: #666;
  font-size: 14px;
}
</style>

<script>
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Scroll vers le don mis en évidence
    document.addEventListener('DOMContentLoaded', () => {
        const highlightedDon = document.getElementById('highlighted-don');
        if (highlightedDon) {
            setTimeout(() => {
                highlightedDon.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 300);
        }
        
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
    });
</script>

</body>
</html>
