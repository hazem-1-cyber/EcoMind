<?php
require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../config.php";

$donCtrl = new DonController();

// Récupérer les filtres
$searchEmail = $_GET['search'] ?? '';
$filterStatut = $_GET['statut'] ?? '';
$filterType = $_GET['type'] ?? '';
$filterDate = $_GET['date'] ?? '';

// Récupérer tous les dons
$allDons = $donCtrl->listDons()->fetchAll();

// Récupérer les types de dons uniques depuis la base de données
$db = Config::getConnexion();
$typesQuery = $db->query("SELECT DISTINCT type_don FROM dons WHERE type_don IS NOT NULL ORDER BY type_don");
$typesDisponibles = $typesQuery->fetchAll(PDO::FETCH_COLUMN);

// Appliquer les filtres
$dons = array_filter($allDons, function($don) use ($searchEmail, $filterStatut, $filterType, $filterDate) {
    // Filtre par email
    if ($searchEmail && stripos($don['email'], $searchEmail) === false) {
        return false;
    }
    
    // Filtre par statut
    if ($filterStatut && $don['statut'] !== $filterStatut) {
        return false;
    }
    
    // Filtre par type
    if ($filterType && $don['type_don'] !== $filterType) {
        return false;
    }
    
    // Filtre par date
    if ($filterDate) {
        $donDate = strtotime($don['created_at']);
        $now = time();
        switch($filterDate) {
            case 'today':
                if (date('Y-m-d', $donDate) !== date('Y-m-d', $now)) return false;
                break;
            case 'week':
                if ($donDate < strtotime('-7 days')) return false;
                break;
            case 'month':
                if ($donDate < strtotime('-30 days')) return false;
                break;
        }
    }
    
    return true;
});

// Pagination
$itemsPerPage = 20; // Nombre de dons par page
$currentPage = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$totalDons = count($dons);
$totalPages = ceil($totalDons / $itemsPerPage);
$offset = ($currentPage - 1) * $itemsPerPage;

// Extraire les dons pour la page actuelle
$donsPaginated = array_slice($dons, $offset, $itemsPerPage);

// Calculer les statistiques (sur tous les dons filtrés, pas seulement la page)
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
  <title>EcoMind - Tous les dons</title>
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
                $donsPendingCount = 0;
                foreach ($dons as $don) {
                    if ($don['statut'] === 'pending') {
                        $donsPendingCount++;
                    }
                }
                ?>
                <div class="header-icon notification-icon" id="notificationBtn" style="position: relative; cursor: pointer;" title="Dons en attente">
                    <i class="fas fa-bell"></i>
                    <?php if ($donsPendingCount > 0): ?>
                        <span class="badge"><?= $donsPendingCount ?></span>
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
                <?php if ($donsPendingCount > 0): ?>
                    <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                            <i class="fas fa-exclamation-circle" style="color: #ff9800; font-size: 24px;"></i>
                            <div>
                                <strong style="color: #856404; font-size: 16px;"><?= $donsPendingCount ?> don(s) en attente</strong>
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
    <h1 class="page-title">Tous les dons reçus</h1>
    <p class="page-subtitle">Historique complet de tous les dons effectués par tous les donateurs.</p>

    <!-- Filtres de recherche -->
    <div class="filters">
        <form method="GET" action="" style="display: contents;">
            <div class="filter-group">
                <label><i class="fas fa-search"></i> Rechercher par email</label>
                <input type="text" name="search" value="<?= htmlspecialchars($searchEmail) ?>" placeholder="exemple@email.com">
            </div>

            <div class="filter-group">
                <label><i class="fas fa-filter"></i> Statut</label>
                <select name="statut">
                    <option value="">Tous</option>
                    <option value="pending" <?= $filterStatut === 'pending' ? 'selected' : '' ?>>En attente</option>
                    <option value="validated" <?= $filterStatut === 'validated' ? 'selected' : '' ?>>Validé</option>
                    <option value="rejected" <?= $filterStatut === 'rejected' ? 'selected' : '' ?>>Rejeté</option>
                    <option value="cancelled" <?= $filterStatut === 'cancelled' ? 'selected' : '' ?>>Annulé</option>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-tag"></i> Type</label>
                <select name="type">
                    <option value="">Tous</option>
                    <?php foreach ($typesDisponibles as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= $filterType === $type ? 'selected' : '' ?>>
                            <?= ucfirst(str_replace('_', ' ', $type)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="filter-group">
                <label><i class="fas fa-calendar"></i> Période</label>
                <select name="date">
                    <option value="">Toutes</option>
                    <option value="today" <?= $filterDate === 'today' ? 'selected' : '' ?>>Aujourd'hui</option>
                    <option value="week" <?= $filterDate === 'week' ? 'selected' : '' ?>>7 derniers jours</option>
                    <option value="month" <?= $filterDate === 'month' ? 'selected' : '' ?>>30 derniers jours</option>
                </select>
            </div>

            <div class="filter-group" style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" class="btn-primary" style="white-space: nowrap;">
                    <i class="fas fa-search"></i> Filtrer
                </button>
                <a href="dons.php" class="btn-secondary" style="padding: 12px 20px; text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>

    <?php if ($searchEmail || $filterStatut || $filterType || $filterDate): ?>
        <div class="alert alert-success" style="margin-bottom: 25px;">
            <i class="fas fa-info-circle"></i>
            <div>
                <strong>Filtres actifs:</strong>
                <?php if ($searchEmail): ?>
                    <span style="display: inline-block; margin: 4px; padding: 4px 12px; background: white; border-radius: 16px; font-size: 13px;">
                        Email: "<?= htmlspecialchars($searchEmail) ?>"
                    </span>
                <?php endif; ?>
                <?php if ($filterStatut): ?>
                    <span style="display: inline-block; margin: 4px; padding: 4px 12px; background: white; border-radius: 16px; font-size: 13px;">
                        Statut: <?= ucfirst($filterStatut) ?>
                    </span>
                <?php endif; ?>
                <?php if ($filterType): ?>
                    <span style="display: inline-block; margin: 4px; padding: 4px 12px; background: white; border-radius: 16px; font-size: 13px;">
                        Type: <?= ucfirst(str_replace('_', ' ', $filterType)) ?>
                    </span>
                <?php endif; ?>
                <?php if ($filterDate): ?>
                    <span style="display: inline-block; margin: 4px; padding: 4px 12px; background: white; border-radius: 16px; font-size: 13px;">
                        Période: <?= $filterDate === 'today' ? "Aujourd'hui" : ($filterDate === 'week' ? '7 jours' : '30 jours') ?>
                    </span>
                <?php endif; ?>
                <span style="margin-left: 8px; font-weight: 700;">
                    <?= $totalDons ?> résultat(s) trouvé(s)
                </span>
            </div>
        </div>
    <?php endif; ?>

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

    <!-- Actions d'export -->
    <div class="export-actions" style="display: flex; gap: 15px; margin-bottom: 25px; justify-content: flex-end;">
        <button onclick="exportToCSV()" class="btn-primary" style="background: #28a745;">
            <i class="fas fa-file-csv"></i> Exporter CSV
        </button>
        <button onclick="window.print()" class="btn-primary" style="background: #6c757d;">
            <i class="fas fa-print"></i> Imprimer
        </button>
    </div>

    <!-- Tableau des dons -->
    <div class="table-container">
      <table id="donsTable">
        <thead>
          <tr>
            <th style="cursor: pointer;" onclick="sortTable(0)">ID <i class="fas fa-sort"></i></th>
            <th style="cursor: pointer;" onclick="sortTable(1)">Date <i class="fas fa-sort"></i></th>
            <th style="cursor: pointer;" onclick="sortTable(2)">Email Donateur <i class="fas fa-sort"></i></th>
            <th style="cursor: pointer;" onclick="sortTable(3)">Type <i class="fas fa-sort"></i></th>
            <th style="cursor: pointer;" onclick="sortTable(4)">Montant <i class="fas fa-sort"></i></th>
            <th style="cursor: pointer;" onclick="sortTable(5)">Ville <i class="fas fa-sort"></i></th>
            <th>Image</th>
            <th style="cursor: pointer;" onclick="sortTable(7)">Statut <i class="fas fa-sort"></i></th>
            <th class="no-print">Détails</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($dons)): ?>
            <tr>
              <td colspan="9" class="empty-message">
                Aucun don enregistré pour le moment.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($donsPaginated as $don): ?>
              <tr>
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
                <td>
                  <?php if (!empty($don['image_path'])): ?>
                    <img src="../../<?= htmlspecialchars($don['image_path']) ?>" 
                         alt="Image du don" 
                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; cursor: pointer; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"
                         onclick="showImageModal('../../<?= htmlspecialchars($don['image_path']) ?>')"
                         title="Cliquer pour agrandir">
                  <?php else: ?>
                    <span style="color: #999; font-size: 12px;">Aucune image</span>
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
                    case 'cancelled':
                      $statutClass = 'statut-cancelled';
                      $statutLabel = 'Annulé';
                      break;
                  }
                  ?>
                  <span class="statut-badge <?= $statutClass ?>"><?= $statutLabel ?></span>
                </td>
                <td class="no-print">
                  <button onclick="showDetails(<?= htmlspecialchars(json_encode($don)) ?>)" class="btn-icon btn-view" title="Voir détails">
                    <i class="fas fa-eye"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination" style="display: flex; justify-content: center; align-items: center; gap: 10px; margin-top: 30px; flex-wrap: wrap;">
        <?php
        // Construire l'URL de base avec les filtres
        $baseUrl = 'dons.php?';
        $params = [];
        if ($searchEmail) $params[] = 'search=' . urlencode($searchEmail);
        if ($filterStatut) $params[] = 'statut=' . urlencode($filterStatut);
        if ($filterType) $params[] = 'type=' . urlencode($filterType);
        if ($filterDate) $params[] = 'date=' . urlencode($filterDate);
        $baseUrl .= implode('&', $params);
        $baseUrl .= ($params ? '&' : '') . 'page=';
        ?>
        
        <!-- Bouton Première page -->
        <?php if ($currentPage > 1): ?>
            <a href="<?= $baseUrl ?>1" class="pagination-btn" style="padding: 10px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #013220; font-weight: 600; transition: all 0.3s;">
                <i class="fas fa-angle-double-left"></i>
            </a>
            <a href="<?= $baseUrl . ($currentPage - 1) ?>" class="pagination-btn" style="padding: 10px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #013220; font-weight: 600; transition: all 0.3s;">
                <i class="fas fa-angle-left"></i> Précédent
            </a>
        <?php endif; ?>
        
        <!-- Numéros de page -->
        <?php
        $startPage = max(1, $currentPage - 2);
        $endPage = min($totalPages, $currentPage + 2);
        
        for ($i = $startPage; $i <= $endPage; $i++):
        ?>
            <a href="<?= $baseUrl . $i ?>" 
               class="pagination-btn <?= $i === $currentPage ? 'active' : '' ?>" 
               style="padding: 10px 15px; background: <?= $i === $currentPage ? 'linear-gradient(135deg, #2c5f2d, #88b04b)' : '#f8f9fa' ?>; border: 1px solid <?= $i === $currentPage ? '#2c5f2d' : '#dee2e6' ?>; border-radius: 8px; text-decoration: none; color: <?= $i === $currentPage ? 'white' : '#013220' ?>; font-weight: 600; transition: all 0.3s; min-width: 40px; text-align: center;">
                <?= $i ?>
            </a>
        <?php endfor; ?>
        
        <!-- Bouton Dernière page -->
        <?php if ($currentPage < $totalPages): ?>
            <a href="<?= $baseUrl . ($currentPage + 1) ?>" class="pagination-btn" style="padding: 10px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #013220; font-weight: 600; transition: all 0.3s;">
                Suivant <i class="fas fa-angle-right"></i>
            </a>
            <a href="<?= $baseUrl . $totalPages ?>" class="pagination-btn" style="padding: 10px 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; text-decoration: none; color: #013220; font-weight: 600; transition: all 0.3s;">
                <i class="fas fa-angle-double-right"></i>
            </a>
        <?php endif; ?>
        
        <!-- Info page -->
        <span style="color: #6c757d; font-size: 14px; margin-left: 15px;">
            Page <?= $currentPage ?> sur <?= $totalPages ?> (<?= $totalDons ?> résultat<?= $totalDons > 1 ? 's' : '' ?>)
        </span>
    </div>
    
    <style>
    .pagination-btn:hover:not(.active) {
        background: #e9ecef !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    </style>
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
@media print {
    .sidebar, .top-header, .filters, .export-actions, .no-print {
        display: none !important;
    }
    .main-content {
        margin-left: 0 !important;
        padding: 20px !important;
    }
    .table-container {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
    }
    table {
        font-size: 12px !important;
    }
}
</style>

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

    // Fonction de tri des colonnes
    let sortDirection = {};
    
    function sortTable(columnIndex) {
        const table = document.getElementById('donsTable');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));
        
        // Ignorer si pas de données
        if (rows.length === 0 || rows[0].cells.length === 1) return;
        
        // Déterminer la direction du tri
        sortDirection[columnIndex] = !sortDirection[columnIndex];
        const isAscending = sortDirection[columnIndex];
        
        // Trier les lignes
        rows.sort((a, b) => {
            let aValue = a.cells[columnIndex].textContent.trim();
            let bValue = b.cells[columnIndex].textContent.trim();
            
            // Gestion des nombres
            if (!isNaN(aValue) && !isNaN(bValue)) {
                aValue = parseFloat(aValue);
                bValue = parseFloat(bValue);
            }
            
            if (aValue < bValue) return isAscending ? -1 : 1;
            if (aValue > bValue) return isAscending ? 1 : -1;
            return 0;
        });
        
        // Réorganiser le tableau
        rows.forEach(row => tbody.appendChild(row));
        
        // Mettre à jour les icônes de tri
        const headers = table.querySelectorAll('th');
        headers.forEach((header, index) => {
            const icon = header.querySelector('i');
            if (icon) {
                if (index === columnIndex) {
                    icon.className = isAscending ? 'fas fa-sort-up' : 'fas fa-sort-down';
                } else {
                    icon.className = 'fas fa-sort';
                }
            }
        });
    }

    // Fonction d'export CSV
    function exportToCSV() {
        const table = document.getElementById('donsTable');
        const rows = table.querySelectorAll('tr');
        let csv = [];
        
        for (let i = 0; i < rows.length; i++) {
            const row = [];
            const cols = rows[i].querySelectorAll('td, th');
            
            for (let j = 0; j < cols.length - 1; j++) { // -1 pour exclure la colonne Actions
                let data = cols[j].textContent.trim();
                data = data.replace(/"/g, '""'); // Échapper les guillemets
                row.push('"' + data + '"');
            }
            
            csv.push(row.join(','));
        }
        
        // Créer le fichier CSV
        const csvContent = csv.join('\n');
        const blob = new Blob(['\ufeff' + csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', 'dons_ecomind_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Fonction pour afficher les détails d'un don
    function showDetails(don) {
        let details = `
            <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; margin: 0 auto;">
                <h2 style="color: #013220; margin-bottom: 20px; border-bottom: 2px solid #A8E6CF; padding-bottom: 10px;">
                    <i class="fas fa-info-circle"></i> Détails du don #${don.id}
                </h2>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <div>
                        <strong style="color: #666;">Date:</strong><br>
                        <span>${new Date(don.created_at).toLocaleDateString('fr-FR')}</span>
                    </div>
                    <div>
                        <strong style="color: #666;">Statut:</strong><br>
                        <span>${don.statut}</span>
                    </div>
                    <div>
                        <strong style="color: #666;">Email:</strong><br>
                        <span>${don.email}</span>
                    </div>
                    <div>
                        <strong style="color: #666;">Type:</strong><br>
                        <span>${don.type_don.replace('_', ' ')}</span>
                    </div>
                </div>
                
                ${don.type_don === 'money' && don.montant ? `
                    <div style="background: #f0f9f4; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <strong style="color: #013220;">Montant:</strong> 
                        <span style="font-size: 24px; font-weight: bold; color: #013220;">${don.montant} TND</span>
                    </div>
                ` : ''}
                
                ${don.ville ? `
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Ville:</strong> ${don.ville}<br>
                        ${don.cp ? `<strong style="color: #666;">Code postal:</strong> ${don.cp}<br>` : ''}
                        ${don.tel ? `<strong style="color: #666;">Téléphone:</strong> ${don.tel}<br>` : ''}
                        ${don.localisation ? `<strong style="color: #666;">Localisation:</strong> <a href="${don.localisation}" target="_blank">Voir sur la carte</a><br>` : ''}
                    </div>
                ` : ''}
                
                ${don.description_don ? `
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Description:</strong><br>
                        <p style="background: #f8f9fa; padding: 10px; border-radius: 6px; margin-top: 5px;">${don.description_don}</p>
                    </div>
                ` : ''}
                
                ${don.image_path ? `
                    <div style="margin-bottom: 15px;">
                        <strong style="color: #666;">Image du don:</strong><br>
                        <img src="../../${don.image_path}" alt="Image du don" style="max-width: 100%; height: auto; border-radius: 8px; margin-top: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); cursor: pointer;" onclick="showImageModal('../../${don.image_path}')">
                    </div>
                ` : ''}
                
                <div style="text-align: center; margin-top: 25px;">
                    <button onclick="closeModal()" style="background: #013220; color: white; border: none; padding: 12px 30px; border-radius: 8px; cursor: pointer; font-size: 16px;">
                        Fermer
                    </button>
                </div>
            </div>
        `;
        
        // Créer la modal
        const modal = document.createElement('div');
        modal.id = 'detailsModal';
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 10000; padding: 20px; overflow-y: auto;';
        modal.innerHTML = details;
        
        modal.onclick = function(e) {
            if (e.target === modal) closeModal();
        };
        
        document.body.appendChild(modal);
    }

    function closeModal() {
        const modal = document.getElementById('detailsModal');
        if (modal) modal.remove();
    }

    // Fonction pour afficher l'image en grand
    function showImageModal(imagePath) {
        const modal = document.createElement('div');
        modal.id = 'imageModal';
        modal.style.cssText = 'position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); display: flex; align-items: center; justify-content: center; z-index: 10000; padding: 20px;';
        
        modal.innerHTML = `
            <div style="position: relative; max-width: 90%; max-height: 90%;">
                <button onclick="closeImageModal()" style="position: absolute; top: -40px; right: 0; background: white; border: none; color: #333; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 24px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.3);">
                    ×
                </button>
                <img src="${imagePath}" alt="Image du don" style="max-width: 100%; max-height: 80vh; border-radius: 12px; box-shadow: 0 8px 30px rgba(0,0,0,0.5);">
            </div>
        `;
        
        modal.onclick = function(e) {
            if (e.target === modal) closeImageModal();
        };
        
        document.body.appendChild(modal);
    }

    function closeImageModal() {
        const modal = document.getElementById('imageModal');
        if (modal) modal.remove();
    }
</script>

</body>
</html>