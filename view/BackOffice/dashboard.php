<?php
require_once __DIR__ . "/../../controller/DonController.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDons()->fetchAll();

// Calculer les statistiques
$totalDons = count($dons);
$totalCollecte = 0;
$donsValides = 0;
$donsPending = 0;
$donsRejected = 0;
$donsCancelled = 0;

// Collecte par mois
$currentYear = date('Y');
$collecteParMois = array_fill(1, 12, 0);

foreach ($dons as $don) {
    if ($don['type_don'] === 'money' && $don['montant']) {
        $totalCollecte += $don['montant'];
        
        // Calculer la collecte par mois
        $month = (int)date('m', strtotime($don['created_at']));
        $year = date('Y', strtotime($don['created_at']));
        if ($year == $currentYear) {
            $collecteParMois[$month] += $don['montant'];
        }
    }
    if ($don['statut'] === 'validated') {
        $donsValides++;
    }
    if ($don['statut'] === 'pending') {
        $donsPending++;
    }
    if ($don['statut'] === 'rejected') {
        $donsRejected++;
    }
    if ($don['statut'] === 'cancelled') {
        $donsCancelled++;
    }
}

$pourcentageValides = $totalDons > 0 ? round(($donsValides / $totalDons) * 100) : 0;
$pourcentagePending = $totalDons > 0 ? round(($donsPending / $totalDons) * 100) : 0;
$pourcentageRejected = $totalDons > 0 ? round(($donsRejected / $totalDons) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind Dashboard - BackOffice</title>
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
            <a href="dashboard.php" class="nav-item active">
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

        <script>
        document.addEventListener('DOMContentLoaded', () => {
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

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $totalDons ?></h3>
                    <p>Total des dons</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="stat-content">
                    <h3><?= number_format($totalCollecte, 2) ?> TND</h3>
                    <p>Montant collecté</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $donsValides ?></h3>
                    <p>Dons validés</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $donsPending ?></h3>
                    <p>En attente</p>
                </div>
            </div>
        </div>

        <!-- Objectifs de collecte -->
        <div class="chart-card" style="margin-top: 25px;">
            <div class="chart-header">
                <h2>🎯 Objectifs de collecte</h2>
            </div>
            <div style="padding: 25px;">
                <?php
                // Objectif mensuel (peut être configuré dans settings.json)
                require_once __DIR__ . "/../../config/SettingsManager.php";
                $settingsManager = new SettingsManager();
                $objectifMensuel = $settingsManager->get('objectif_mensuel', 10000);
                $pourcentageObjectif = min(100, round(($totalCollecte / $objectifMensuel) * 100));
                ?>
                
                <div style="margin-bottom: 30px;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                        <span style="font-weight: 600; color: #2c5f2d;">Objectif mensuel</span>
                        <span style="font-weight: 700; color: #2c5f2d; font-size: 18px;">
                            <?= number_format($totalCollecte, 2) ?> / <?= number_format($objectifMensuel, 2) ?> TND
                        </span>
                    </div>
                    <div style="background: #e9ecef; height: 30px; border-radius: 15px; overflow: hidden; position: relative;">
                        <div style="background: linear-gradient(90deg, #2c5f2d, #88b04b, #A8E6CF); height: 100%; width: <?= $pourcentageObjectif ?>%; transition: width 1s ease; display: flex; align-items: center; justify-content: flex-end; padding-right: 15px; color: white; font-weight: 700; font-size: 14px;">
                            <?= $pourcentageObjectif ?>%
                        </div>
                    </div>
                    <div style="margin-top: 8px; color: #6c757d; font-size: 14px;">
                        <?php if ($pourcentageObjectif >= 100): ?>
                            🎉 Objectif atteint ! Félicitations !
                        <?php else: ?>
                            Il reste <?= number_format($objectifMensuel - $totalCollecte, 2) ?> TND pour atteindre l'objectif
                        <?php endif; ?>
                    </div>
                </div>

                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                    <div style="background: linear-gradient(135deg, #e3f2fd, #bbdefb); padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #1976d2;">
                            <?= number_format($totalCollecte / max(1, $donsValides), 2) ?> TND
                        </div>
                        <div style="color: #1565c0; font-size: 14px; margin-top: 5px;">Don moyen</div>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #f3e5f5, #e1bee7); padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #7b1fa2;">
                            <?= $donsPending ?>
                        </div>
                        <div style="color: #6a1b9a; font-size: 14px; margin-top: 5px;">À traiter</div>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #fff3e0, #ffe0b2); padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #f57c00;">
                            <?= count(array_filter($dons, fn($d) => $d['type_don'] === 'money')) ?>
                        </div>
                        <div style="color: #ef6c00; font-size: 14px; margin-top: 5px;">Dons monétaires</div>
                    </div>
                    
                    <div style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); padding: 20px; border-radius: 12px; text-align: center;">
                        <div style="font-size: 28px; font-weight: 700; color: #388e3c;">
                            <?= count(array_filter($dons, fn($d) => $d['type_don'] !== 'money')) ?>
                        </div>
                        <div style="color: #2e7d32; font-size: 14px; margin-top: 5px;">Dons matériels</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques Statuts et Collecte -->
        <div class="charts-grid" style="margin-top: 25px;">
            <!-- Graphique en cercle - Statuts des dons -->
            <div class="chart-card">
                <div class="chart-header">
                    <h2><i class="fas fa-chart-pie"></i> Répartition par statut</h2>
                </div>
                <div class="chart-body" style="height: 350px;">
                    <canvas id="statusChart"></canvas>
                </div>
                <div style="padding: 15px; display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #28a745; border-radius: 3px;"></div>
                        <span style="font-size: 14px;">Acceptés (<?= $donsValides ?>)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #ffc107; border-radius: 3px;"></div>
                        <span style="font-size: 14px;">En attente (<?= $donsPending ?>)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #dc3545; border-radius: 3px;"></div>
                        <span style="font-size: 14px;">Rejetés (<?= $donsRejected ?>)</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 8px;">
                        <div style="width: 16px; height: 16px; background: #6c757d; border-radius: 3px;"></div>
                        <span style="font-size: 14px;">Annulés (<?= $donsCancelled ?>)</span>
                    </div>
                </div>
            </div>

            <!-- Graphique collecte monétaire par mois -->
            <div class="chart-card">
                <div class="chart-header">
                    <h2><i class="fas fa-coins"></i> Collecte monétaire par mois (<?= $currentYear ?>)</h2>
                </div>
                <div class="chart-body" style="height: 350px;">
                    <canvas id="collecteChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="charts-grid">
            <div class="chart-card">
                <div class="chart-header">
                    <h2>Évolution des dons</h2>
                </div>
                <div class="chart-body">
                    <canvas id="donationsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Donations -->
        <div class="chart-card" style="margin-top: 25px;">
            <div class="chart-header">
                <h2>Derniers dons</h2>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        // Récupérer les 5 derniers dons (ordre décroissant)
                        $recentDons = $donCtrl->listRecentDons(5);
                        foreach ($recentDons as $don): 
                        ?>
                            <tr>
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
                                <td>
                                    <?php
                                    $statutClass = '';
                                    switch($don['statut']) {
                                        case 'pending':
                                            $statutClass = 'statut-pending';
                                            $label = 'En attente';
                                            break;
                                        case 'validated':
                                            $statutClass = 'statut-validated';
                                            $label = 'Validé';
                                            break;
                                        case 'rejected':
                                            $statutClass = 'statut-rejected';
                                            $label = 'Rejeté';
                                            break;
                                        default:
                                            $statutClass = 'statut-cancelled';
                                            $label = 'Annulé';
                                    }
                                    ?>
                                    <span class="statut-badge <?= $statutClass ?>"><?= $label ?></span>
                                </td>
                                <td>
                                    <a href="lisdon.php?don_id=<?= $don['id'] ?>" class="btn-icon btn-view" title="Gérer">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Données PHP vers JavaScript
    const dons = <?= json_encode($dons) ?>;
    const collecteParMois = <?= json_encode(array_values($collecteParMois)) ?>;

    // Chart 0: Graphique en cercle - Statuts des dons
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Acceptés', 'En attente', 'Rejetés', 'Annulés'],
            datasets: [{
                data: [<?= $donsValides ?>, <?= $donsPending ?>, <?= $donsRejected ?>, <?= $donsCancelled ?>],
                backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                borderWidth: 3,
                borderColor: '#fff',
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.95)',
                    padding: 15,
                    titleFont: { size: 16, weight: 'bold' },
                    bodyFont: { size: 14 },
                    borderColor: '#A8E6CF',
                    borderWidth: 2,
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0;
                            return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Chart 1: Collecte monétaire par mois
    const collecteCtx = document.getElementById('collecteChart').getContext('2d');
    new Chart(collecteCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Collecte (TND)',
                data: collecteParMois,
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: '#ffc107',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.95)',
                    padding: 15,
                    callbacks: {
                        label: function(context) {
                            return 'Collecte: ' + context.parsed.y.toFixed(2) + ' TND';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return value + ' TND';
                        }
                    }
                }
            }
        }
    });

    // Chart 2: Évolution des dons
    const donationsCtx = document.getElementById('donationsChart').getContext('2d');
    new Chart(donationsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin'],
            datasets: [{
                label: 'Dons reçus',
                data: [12, 19, 15, 25, 22, <?= $totalDons ?>],
                borderColor: '#A8E6CF',
                backgroundColor: 'rgba(168, 230, 207, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

</body>
</html>
