<?php
require_once __DIR__ . "/../../controller/DonController.php";
require_once __DIR__ . "/../../config.php";

$donCtrl = new DonController();
$dons = $donCtrl->listDons()->fetchAll();

// Récupérer toutes les catégories depuis la base de données
$db = config::getConnexion();
$stmt = $db->query("SELECT code, nom FROM categories ORDER BY nom");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculer les statistiques des donateurs
$donateurStats = [];
foreach ($dons as $don) {
    $email = $don['email'];
    if (!isset($donateurStats[$email])) {
        $donateurStats[$email] = [
            'email' => $email,
            'nombre_dons' => 0,
            'montant_total' => 0,
            'derniere_date' => $don['created_at']
        ];
    }
    $donateurStats[$email]['nombre_dons']++;
    if ($don['type_don'] === 'money' && $don['montant']) {
        $donateurStats[$email]['montant_total'] += $don['montant'];
    }
    if (strtotime($don['created_at']) > strtotime($donateurStats[$email]['derniere_date'])) {
        $donateurStats[$email]['derniere_date'] = $don['created_at'];
    }
}

// Trier par nombre de dons décroissant (puis par montant si égalité)
usort($donateurStats, function($a, $b) {
    if ($b['nombre_dons'] === $a['nombre_dons']) {
        return $b['montant_total'] <=> $a['montant_total'];
    }
    return $b['nombre_dons'] <=> $a['nombre_dons'];
});

// Compter les dons en attente pour les notifications
$donsPending = 0;
foreach ($dons as $don) {
    if ($don['statut'] === 'pending') {
        $donsPending++;
    }
}

// Fonction pour extraire l'abréviation d'un nom d'association
function getAssociationAbbreviation($nom) {
    // Si le nom commence par une abréviation connue, l'extraire
    if (preg_match('/^([A-Z]{2,})\s*[-–—]\s*/', $nom, $matches)) {
        return $matches[1];
    }
    
    // Sinon, prendre les premières lettres des mots en majuscules
    $words = explode(' ', $nom);
    $abbr = '';
    foreach ($words as $word) {
        if (preg_match('/^[A-Z]/', $word)) {
            $abbr .= $word[0];
        }
    }
    
    // Si on a une abréviation, la retourner, sinon retourner les 4 premiers caractères
    return $abbr ?: substr($nom, 0, 4);
}

// Récupérer les statistiques par association
$stmtAssoc = $db->query("SELECT id, nom FROM associations ORDER BY nom");
$associations = $stmtAssoc->fetchAll(PDO::FETCH_ASSOC);

$donsByAssociation = [];
$montantByAssociation = [];
foreach ($associations as $assoc) {
    $donsByAssociation[$assoc['id']] = [
        'nom' => $assoc['nom'],
        'abbr' => getAssociationAbbreviation($assoc['nom']),
        'nombre' => 0,
        'montant' => 0
    ];
}

// Compter les dons par association
foreach ($dons as $don) {
    $assocId = $don['association_id'];
    if (isset($donsByAssociation[$assocId])) {
        $donsByAssociation[$assocId]['nombre']++;
        if ($don['type_don'] === 'money' && $don['montant']) {
            $donsByAssociation[$assocId]['montant'] += $don['montant'];
        }
    }
}

// Trier par nombre de dons décroissant
uasort($donsByAssociation, function($a, $b) {
    return $b['nombre'] <=> $a['nombre'];
});

// Calculer les statistiques par mois
$donsByMonth = [];
$currentYear = date('Y');
for ($i = 1; $i <= 12; $i++) {
    $donsByMonth[$i] = 0;
}

foreach ($dons as $don) {
    $month = (int)date('m', strtotime($don['created_at']));
    $year = date('Y', strtotime($don['created_at']));
    if ($year == $currentYear) {
        $donsByMonth[$month]++;
    }
}

// Initialiser les compteurs pour toutes les catégories dynamiquement
$donsByType = [];
$montantByType = [];
$categoryNames = [];

// Ajouter toutes les catégories de la base de données
foreach ($categories as $cat) {
    $donsByType[$cat['code']] = 0;
    $montantByType[$cat['code']] = 0;
    $categoryNames[$cat['code']] = $cat['nom'];
}

// Compter les dons par type
foreach ($dons as $don) {
    $type = $don['type_don'];
    if (isset($donsByType[$type])) {
        $donsByType[$type]++;
        if ($type === 'money' && $don['montant']) {
            $montantByType[$type] += $don['montant'];
        }
    } else {
        // Si un type de don n'est pas dans les catégories, l'ajouter
        $donsByType[$type] = 1;
        $montantByType[$type] = 0;
        $categoryNames[$type] = ucfirst(str_replace('_', ' ', $type));
    }
}

$totalDons = count($dons);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>EcoMind - Statistiques</title>
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
            <a href="associations.php" class="nav-item">
                <i class="fas fa-building"></i>
                <span>Associations</span>
            </a>
            <a href="statistiques.php" class="nav-item active">
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
            <h1 class="page-title">Statistiques Détaillées</h1>
            <p class="page-subtitle">Analyse et visualisation des données de dons</p>

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

                <?php 
                $index = 0;
                $icons = ['fa-coins', 'fa-solar-panel', 'fa-box', 'fa-gift', 'fa-tools', 'fa-leaf', 'fa-seedling'];
                foreach ($categories as $cat): 
                    if ($index >= 3) break; // Afficher les 3 premières catégories
                ?>
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas <?= $icons[$index % count($icons)] ?>"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $donsByType[$cat['code']] ?></h3>
                        <p><?= htmlspecialchars($cat['nom']) ?></p>
                    </div>
                </div>
                <?php 
                    $index++;
                endforeach; 
                ?>
            </div>

            <!-- Statistiques par Association -->
            <div class="chart-card" style="margin-top: 25px;">
                <div class="chart-header">
                    <h2><i class="fas fa-building"></i> Statistiques par Association</h2>
                </div>
                <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; padding: 20px;">
                    <?php 
                    $assocIcons = ['fa-leaf', 'fa-recycle', 'fa-tree', 'fa-seedling'];
                    $assocColors = ['#A8E6CF', '#FFD93D', '#6BCF7F', '#4D96FF'];
                    $assocIndex = 0;
                    foreach ($donsByAssociation as $assocId => $assocData): 
                    ?>
                    <div class="stat-card" style="background: linear-gradient(135deg, <?= $assocColors[$assocIndex % count($assocColors)] ?> 0%, <?= $assocColors[$assocIndex % count($assocColors)] ?>dd 100%); border: none;">
                        <div class="stat-icon" style="background: rgba(255, 255, 255, 0.3);">
                            <i class="fas <?= $assocIcons[$assocIndex % count($assocIcons)] ?>" style="color: #013220;"></i>
                        </div>
                        <div class="stat-content">
                            <h3 style="color: #013220; font-size: 32px; font-weight: 700;"><?= $assocData['nombre'] ?></h3>
                            <p style="color: #013220; font-weight: 600; font-size: 14px;" title="<?= htmlspecialchars($assocData['nom']) ?>">
                                <?= strlen($assocData['nom']) > 30 ? htmlspecialchars($assocData['abbr']) : htmlspecialchars($assocData['nom']) ?>
                            </p>
                            <?php if ($assocData['montant'] > 0): ?>
                                <p style="color: #2c5f2d; font-size: 13px; margin-top: 5px; font-weight: 600;">
                                    💰 <?= number_format($assocData['montant'], 2) ?> TND
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php 
                        $assocIndex++;
                    endforeach; 
                    ?>
                </div>
                
                <!-- Graphique en barres des associations -->
                <div class="chart-body" style="height: 300px; padding: 20px;">
                    <canvas id="associationsChart"></canvas>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="charts-grid">
                <!-- Évolution par mois -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h2><i class="fas fa-calendar-alt"></i> Évolution des dons par mois (<?= $currentYear ?>)</h2>
                    </div>
                    <div class="chart-body">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <!-- Répartition par type -->
                <div class="chart-card">
                    <div class="chart-header">
                        <h2><i class="fas fa-chart-pie"></i> Répartition par type de don</h2>
                    </div>
                    <div class="chart-body">
                        <canvas id="typeChart"></canvas>
                    </div>
                    <div class="chart-legend">
                        <?php 
                        $colors = ['#A8E6CF', '#FFD93D', '#6BCF7F', '#4D96FF', '#FF6B9D', '#C77DFF', '#06FFA5', '#FF9E00'];
                        $colorIndex = 0;
                        foreach ($categories as $cat): 
                        ?>
                        <div class="legend-item">
                            <div class="legend-color" style="background: <?= $colors[$colorIndex % count($colors)] ?>;"></div>
                            <span><?= htmlspecialchars($cat['nom']) ?> (<?= $donsByType[$cat['code']] ?>)</span>
                        </div>
                        <?php 
                            $colorIndex++;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>

            <!-- Detailed Stats Table -->
            <div class="chart-card" style="margin-top: 25px;">
                <div class="chart-header">
                    <h2><i class="fas fa-table"></i> Statistiques détaillées par type</h2>
                </div>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Type de don</th>
                                <th>Nombre</th>
                                <th>Pourcentage</th>
                                <th>Montant total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $icons = ['fa-coins', 'fa-solar-panel', 'fa-box', 'fa-gift', 'fa-tools', 'fa-leaf', 'fa-seedling', 'fa-recycle'];
                            $colors = ['#A8E6CF', '#FFD93D', '#6BCF7F', '#4D96FF', '#FF6B9D', '#C77DFF', '#06FFA5', '#FF9E00'];
                            $iconIndex = 0;
                            foreach ($categories as $cat): 
                            ?>
                            <tr>
                                <td><i class="fas <?= $icons[$iconIndex % count($icons)] ?>" style="color: <?= $colors[$iconIndex % count($colors)] ?>; margin-right: 8px;"></i> <?= htmlspecialchars($cat['nom']) ?></td>
                                <td><?= $donsByType[$cat['code']] ?></td>
                                <td><?= $totalDons > 0 ? round(($donsByType[$cat['code']] / $totalDons) * 100, 1) : 0 ?>%</td>
                                <td><?= $cat['code'] === 'money' && $montantByType[$cat['code']] > 0 ? number_format($montantByType[$cat['code']], 2) . ' TND' : '-' ?></td>
                            </tr>
                            <?php 
                                $iconIndex++;
                            endforeach; 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Histogramme Top 10 Meilleurs Donneurs -->
            <?php 
            $top10Donateurs = array_slice($donateurStats, 0, 10);
            ?>
            <div class="chart-card" style="margin-top: 25px;">
                <div class="chart-header">
                    <h2><i class="fas fa-trophy"></i> <i class="fas fa-users"></i> Top 10 Meilleurs Donneurs</h2>
                </div>
                <div class="chart-body" style="height: 400px;">
                    <canvas id="top10Chart"></canvas>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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

    // Données PHP vers JavaScript
    const donsByMonth = <?= json_encode(array_values($donsByMonth)) ?>;
    const donsByType = <?= json_encode(array_values($donsByType)) ?>;
    const categoryLabels = <?= json_encode(array_values($categoryNames)) ?>;
    const categoryColors = ['#A8E6CF', '#FFD93D', '#6BCF7F', '#4D96FF', '#FF6B9D', '#C77DFF', '#06FFA5', '#FF9E00'];
    
    // Données des associations
    const associationsData = <?= json_encode(array_values(array_map(function($a) {
        return [
            'nom' => $a['nom'],
            'abbr' => $a['abbr'],
            'nombre' => $a['nombre'],
            'montant' => $a['montant']
        ];
    }, $donsByAssociation))) ?>;
    
    const associationsLabels = associationsData.map(a => a.abbr); // Utiliser les abréviations
    const associationsNomComplets = associationsData.map(a => a.nom); // Garder les noms complets pour les tooltips
    const associationsNombre = associationsData.map(a => a.nombre);
    const associationsMontant = associationsData.map(a => a.montant);
    
    // Données Top 10 Donneurs
    const top10Donneurs = <?= json_encode(array_map(function($d) {
        return [
            'email' => $d['email'],
            'nombre_dons' => $d['nombre_dons']
        ];
    }, $top10Donateurs)) ?>;
    
    const top10Labels = top10Donneurs.map(d => {
        const email = d.email;
        return email.length > 20 ? email.substring(0, 20) + '...' : email;
    });
    const top10Data = top10Donneurs.map(d => d.nombre_dons);

    // Chart Associations: Graphique en barres des associations
    const associationsCtx = document.getElementById('associationsChart').getContext('2d');
    new Chart(associationsCtx, {
        type: 'bar',
        data: {
            labels: associationsLabels,
            datasets: [{
                label: 'Nombre de dons',
                data: associationsNombre,
                backgroundColor: ['#A8E6CF', '#FFD93D', '#6BCF7F', '#4D96FF'],
                borderColor: ['#88b04b', '#FFC107', '#4CAF50', '#2196F3'],
                borderWidth: 2,
                borderRadius: 10,
                borderSkipped: false
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
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: '#A8E6CF',
                    borderWidth: 2,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            const index = context[0].dataIndex;
                            return associationsNomComplets[index]; // Afficher le nom complet
                        },
                        label: function(context) {
                            const index = context.dataIndex;
                            const montant = associationsMontant[index];
                            let label = 'Nombre de dons: ' + context.parsed.y;
                            if (montant > 0) {
                                label += '\nMontant total: ' + montant.toFixed(2) + ' TND';
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#013220',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: 'rgba(168, 230, 207, 0.2)'
                    }
                },
                x: {
                    ticks: {
                        color: '#013220',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Chart 0: Histogramme Top 10 Meilleurs Donneurs
    const top10Ctx = document.getElementById('top10Chart').getContext('2d');
    new Chart(top10Ctx, {
        type: 'bar',
        data: {
            labels: top10Labels,
            datasets: [{
                label: 'Nombre de dons',
                data: top10Data,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ],
                borderColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 206, 86)',
                    'rgb(75, 192, 192)',
                    'rgb(153, 102, 255)',
                    'rgb(255, 159, 64)',
                    'rgb(168, 230, 207)',
                    'rgb(136, 176, 75)',
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.95)',
                    padding: 15,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: '#A8E6CF',
                    borderWidth: 2,
                    displayColors: false,
                    callbacks: {
                        title: function(context) {
                            return top10Donneurs[context[0].dataIndex].email;
                        },
                        label: function(context) {
                            return 'Nombre de dons: ' + context.parsed.x;
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#013220',
                        font: {
                            size: 12,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: 'rgba(168, 230, 207, 0.2)'
                    },
                    title: {
                        display: true,
                        text: 'Nombre de dons',
                        color: '#2c5f2d',
                        font: {
                            size: 14,
                            weight: 'bold'
                        }
                    }
                },
                y: {
                    ticks: {
                        color: '#013220',
                        font: {
                            size: 11,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Chart 1: Histogramme - Évolution mensuelle
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'],
            datasets: [{
                label: 'Nombre de dons',
                data: donsByMonth,
                backgroundColor: [
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)',
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)',
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)',
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)',
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)',
                    'rgba(168, 230, 207, 0.8)',
                    'rgba(136, 176, 75, 0.8)'
                ],
                borderColor: [
                    '#A8E6CF',
                    '#88b04b',
                    '#A8E6CF',
                    '#88b04b',
                    '#A8E6CF',
                    '#88b04b',
                    '#A8E6CF',
                    '#88b04b',
                    '#A8E6CF',
                    '#88b04b',
                    '#A8E6CF',
                    '#88b04b'
                ],
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
                hoverBackgroundColor: 'rgba(44, 95, 45, 0.9)',
                hoverBorderColor: '#2c5f2d',
                hoverBorderWidth: 3
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
                    titleFont: {
                        size: 16,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 14
                    },
                    borderColor: '#A8E6CF',
                    borderWidth: 2,
                    displayColors: false,
                    callbacks: {
                        label: function(context) {
                            return 'Dons: ' + context.parsed.y;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                        color: '#013220',
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    },
                    grid: {
                        color: 'rgba(168, 230, 207, 0.2)',
                        drawBorder: false
                    }
                },
                x: {
                    ticks: {
                        color: '#013220',
                        font: {
                            size: 13,
                            weight: '600'
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            },
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            }
        }
    });

    // Chart 2: Répartition par type (Doughnut)
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    new Chart(typeCtx, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: donsByType,
                backgroundColor: categoryColors.slice(0, categoryLabels.length),
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
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    padding: 12,
                    titleFont: {
                        size: 14,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 13
                    },
                    borderColor: '#A8E6CF',
                    borderWidth: 1,
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
</script>

</body>
</html>
