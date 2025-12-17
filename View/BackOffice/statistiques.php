<?php
// statistiques.php - Vue des statistiques avancées
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - Statistiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">🌱</div>
                <div class="logo-text">EcoMind</div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#events" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Événements</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Inscriptions</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="nav-item">
                <i class="fas fa-lightbulb"></i>
                <span>Propositions</span>
            </a>
            <div class="nav-dropdown" id="searchDropdown">
                <div class="nav-dropdown-toggle">
                    <i class="fas fa-search"></i>
                    <span>Rechercher</span>
                    <i class="fas fa-chevron-down dropdown-arrow"></i>
                </div>
                <div class="nav-dropdown-content">
                    <a href="<?= BASE_URL ?>/index.php?page=search_events" class="nav-sub-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Événements</span>
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?page=search_inscriptions" class="nav-sub-item">
                        <i class="fas fa-users"></i>
                        <span>Inscriptions</span>
                    </a>
                    <a href="<?= BASE_URL ?>/index.php?page=search_propositions" class="nav-sub-item">
                        <i class="fas fa-lightbulb"></i>
                        <span>Propositions</span>
                    </a>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/index.php?page=statistiques" class="nav-item active">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=events" class="nav-item">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au site</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- TOP HEADER -->
        <div class="top-header">
            <div class="header-left">
                <h1><i class="fas fa-chart-line"></i> Statistiques Avancées</h1>
            </div>
            <div class="header-right">
                <div class="header-icon" id="exportBtn" title="Exporter en PDF">
                    <i class="fas fa-download"></i>
                </div>
                <div class="header-icon" id="refreshBtn" title="Actualiser les données">
                    <i class="fas fa-refresh"></i>
                </div>
            </div>
        </div>

        <!-- KPI CARDS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['tauxParticipation'] ?>%</h3>
                    <p>Taux de Participation</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['croissanceMensuelle'] > 0 ? '+' : '' ?><?= $stats['croissanceMensuelle'] ?>%</h3>
                    <p>Croissance Mensuelle</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-friends"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['ageMoyen'] ?> ans</h3>
                    <p>Âge Moyen des Participants</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-content">
                    <h3><?= $stats['tauxConversion'] ?>%</h3>
                    <p>Taux de Conversion</p>
                </div>
            </div>
        </div>

        <!-- CHARTS GRID -->
        <div class="charts-grid">
            <!-- ÉVÉNEMENTS PAR TYPE -->
            <div class="chart-card">
                <div class="chart-header">
                    <h2><i class="fas fa-pie-chart"></i> Événements par Type</h2>
                </div>
                <div class="chart-body">
                    <canvas id="typeChart"></canvas>
                </div>
                <div class="chart-legend" id="typeLegend">
                    <?php foreach($chartsData['evenementsParType'] as $index => $type): ?>
                    <div class="legend-item">
                        <div class="legend-color" style="background: <?= ['#A8E6CF', '#013220', '#2c8f5a', '#66c2a5', '#fc8d62'][$index % 5] ?>"></div>
                        <span><?= htmlspecialchars($type['type']) ?> (<?= $type['pourcentage'] ?>%)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- TOP 3 ÉVÉNEMENTS -->
            <div class="chart-card">
                <div class="chart-header">
                    <h2><i class="fas fa-trophy"></i> Top 3 Événements Populaires</h2>
                </div>
                <div class="chart-body">
                    <canvas id="topEventsChart"></canvas>
                </div>
                <div class="chart-legend">
                    <?php foreach($chartsData['topEvenements'] as $index => $event): ?>
                    <div class="legend-item">
                        <div class="legend-color" style="background: <?= ['#A8E6CF', '#013220', '#2c8f5a'][$index] ?>"></div>
                        <span><?= htmlspecialchars(substr($event['titre'], 0, 30)) ?>... (<?= $event['nb_inscriptions'] ?>)</span>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- DETAILED STATS TABLE -->
        <div style="background: rgba(255, 255, 255, 0.95); padding: 30px; border-radius: 20px; margin-top: 30px; box-shadow: 0 8px 30px rgba(1, 50, 32, 0.08);">
            <h2 style="color: #013220; margin-bottom: 20px;"><i class="fas fa-table"></i> Analyse Détaillée</h2>
            
            <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, rgba(168, 230, 207, 0.1), rgba(168, 230, 207, 0.05)); border-radius: 15px; border: 2px solid #A8E6CF;">
                    <h4 style="color: #013220; margin: 0;">Événements Créés</h4>
                    <p style="font-size: 24px; font-weight: bold; color: #2c8f5a; margin: 10px 0 0 0;"><?= count($chartsData['evenementsParType']) > 0 ? array_sum(array_column($chartsData['evenementsParType'], 'nombre')) : 0 ?></p>
                </div>
                
                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, rgba(168, 230, 207, 0.1), rgba(168, 230, 207, 0.05)); border-radius: 15px; border: 2px solid #A8E6CF;">
                    <h4 style="color: #013220; margin: 0;">Total Inscriptions</h4>
                    <p style="font-size: 24px; font-weight: bold; color: #2c8f5a; margin: 10px 0 0 0;"><?= array_sum(array_column($chartsData['topEvenements'], 'nb_inscriptions')) ?></p>
                </div>
                
                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, rgba(168, 230, 207, 0.1), rgba(168, 230, 207, 0.05)); border-radius: 15px; border: 2px solid #A8E6CF;">
                    <h4 style="color: #013220; margin: 0;">Événement Star</h4>
                    <p style="font-size: 14px; font-weight: bold; color: #2c8f5a; margin: 10px 0 0 0;">
                        <?= isset($chartsData['topEvenements'][0]) ? htmlspecialchars(substr($chartsData['topEvenements'][0]['titre'], 0, 20)) . '...' : 'Aucun' ?>
                    </p>
                </div>
                
                <div style="text-align: center; padding: 20px; background: linear-gradient(135deg, rgba(168, 230, 207, 0.1), rgba(168, 230, 207, 0.05)); border-radius: 15px; border: 2px solid #A8E6CF;">
                    <h4 style="color: #013220; margin: 0;">Type Préféré</h4>
                    <p style="font-size: 14px; font-weight: bold; color: #2c8f5a; margin: 10px 0 0 0;">
                        <?= isset($chartsData['evenementsParType'][0]) ? htmlspecialchars($chartsData['evenementsParType'][0]['type']) : 'Aucun' ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Chart.js Configuration
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.color = '#013220';

// 1. ÉVÉNEMENTS PAR TYPE - PIE CHART
const typeCtx = document.getElementById('typeChart').getContext('2d');
const typeChart = new Chart(typeCtx, {
    type: 'pie',
    data: {
        labels: [<?php echo "'" . implode("','", array_column($chartsData['evenementsParType'], 'type')) . "'"; ?>],
        datasets: [{
            data: [<?php echo implode(',', array_column($chartsData['evenementsParType'], 'nombre')); ?>],
            backgroundColor: ['#A8E6CF', '#013220', '#2c8f5a', '#66c2a5', '#fc8d62'],
            borderWidth: 3,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false // Using custom legend
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    }
});

// 2. TOP 3 ÉVÉNEMENTS - BAR CHART
const topEventsCtx = document.getElementById('topEventsChart').getContext('2d');
const topEventsChart = new Chart(topEventsCtx, {
    type: 'bar',
    data: {
        labels: [<?php echo "'" . implode("','", array_map(function($event) { return substr($event['titre'], 0, 15) . '...'; }, $chartsData['topEvenements'])) . "'"; ?>],
        datasets: [{
            label: 'Inscriptions',
            data: [<?php echo implode(',', array_column($chartsData['topEvenements'], 'nb_inscriptions')); ?>],
            backgroundColor: ['#A8E6CF', '#013220', '#2c8f5a'],
            borderColor: ['#A8E6CF', '#013220', '#2c8f5a'],
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
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
                callbacks: {
                    title: function(context) {
                        // Show full title in tooltip
                        const fullTitles = [<?php echo "'" . implode("','", array_map(function($event) { return addslashes($event['titre']); }, $chartsData['topEvenements'])) . "'"; ?>];
                        return fullTitles[context[0].dataIndex];
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(168, 230, 207, 0.2)'
                },
                ticks: {
                    color: '#013220'
                }
            },
            x: {
                grid: {
                    display: false
                },
                ticks: {
                    color: '#013220'
                }
            }
        }
    }
});

// Auto-refresh every 5 minutes
setTimeout(function() {
    location.reload();
}, 300000);

// Search dropdown functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchDropdown = document.getElementById('searchDropdown');
    if (searchDropdown) {
        searchDropdown.addEventListener('click', function(e) {
            if (e.target.closest('.nav-dropdown-toggle')) {
                e.preventDefault();
                this.classList.toggle('active');
            }
        });
    }
});

// Button functionality
document.getElementById('refreshBtn').addEventListener('click', function() {
    // Add loading animation
    const icon = this.querySelector('i');
    const originalClass = icon.className;
    icon.className = 'fas fa-spinner fa-spin';
    
    // Refresh the page
    setTimeout(function() {
        location.reload();
    }, 500);
});

document.getElementById('exportBtn').addEventListener('click', function() {
    // Add loading animation
    const icon = this.querySelector('i');
    const originalClass = icon.className;
    icon.className = 'fas fa-spinner fa-spin';
    
    // Generate PDF export
    exportStatisticsToPDF();
    
    // Reset icon after 2 seconds
    setTimeout(function() {
        icon.className = originalClass;
    }, 2000);
});

function exportStatisticsToPDF() {
    // Create a comprehensive statistics report
    const reportData = {
        title: 'Rapport Statistiques EcoMind',
        date: new Date().toLocaleDateString('fr-FR'),
        stats: {
            tauxParticipation: '<?= $stats["tauxParticipation"] ?>%',
            croissanceMensuelle: '<?= $stats["croissanceMensuelle"] > 0 ? "+" : "" ?><?= $stats["croissanceMensuelle"] ?>%',
            ageMoyen: '<?= $stats["ageMoyen"] ?> ans',
            tauxConversion: '<?= $stats["tauxConversion"] ?>%'
        },
        evenementsParType: <?= json_encode($chartsData['evenementsParType']) ?>,
        topEvenements: <?= json_encode($chartsData['topEvenements']) ?>
    };
    
    // Create HTML content for PDF
    const htmlContent = `
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Rapport Statistiques EcoMind</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; color: #013220; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #A8E6CF; padding-bottom: 20px; }
                .logo { font-size: 24px; font-weight: bold; color: #013220; margin-bottom: 10px; }
                .date { color: #666; }
                .kpi-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 30px 0; }
                .kpi-card { background: #f8f9fa; padding: 20px; border-radius: 10px; text-align: center; border: 2px solid #A8E6CF; }
                .kpi-value { font-size: 28px; font-weight: bold; color: #2c8f5a; margin-bottom: 5px; }
                .kpi-label { color: #013220; font-weight: 500; }
                .section { margin: 30px 0; }
                .section h2 { color: #013220; border-bottom: 1px solid #A8E6CF; padding-bottom: 10px; }
                table { width: 100%; border-collapse: collapse; margin: 15px 0; }
                th, td { padding: 12px; text-align: left; border-bottom: 1px solid #ddd; }
                th { background-color: #A8E6CF; color: #013220; font-weight: bold; }
                .footer { margin-top: 50px; text-align: center; color: #666; font-size: 12px; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="logo">🌱 EcoMind - Rapport Statistiques</div>
                <div class="date">Généré le ${reportData.date}</div>
            </div>
            
            <div class="kpi-grid">
                <div class="kpi-card">
                    <div class="kpi-value">${reportData.stats.tauxParticipation}</div>
                    <div class="kpi-label">Taux de Participation</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">${reportData.stats.croissanceMensuelle}</div>
                    <div class="kpi-label">Croissance Mensuelle</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">${reportData.stats.ageMoyen}</div>
                    <div class="kpi-label">Âge Moyen des Participants</div>
                </div>
                <div class="kpi-card">
                    <div class="kpi-value">${reportData.stats.tauxConversion}</div>
                    <div class="kpi-label">Taux de Conversion</div>
                </div>
            </div>
            
            <div class="section">
                <h2>📊 Événements par Type</h2>
                <table>
                    <thead>
                        <tr><th>Type d'Événement</th><th>Nombre</th><th>Pourcentage</th></tr>
                    </thead>
                    <tbody>
                        ${reportData.evenementsParType.map(type => 
                            `<tr><td>${type.type}</td><td>${type.nombre}</td><td>${type.pourcentage}%</td></tr>`
                        ).join('')}
                    </tbody>
                </table>
            </div>
            
            <div class="section">
                <h2>🏆 Top 3 Événements Populaires</h2>
                <table>
                    <thead>
                        <tr><th>Rang</th><th>Titre de l'Événement</th><th>Type</th><th>Inscriptions</th></tr>
                    </thead>
                    <tbody>
                        ${reportData.topEvenements.map((event, index) => 
                            `<tr><td>${index + 1}</td><td>${event.titre}</td><td>${event.type}</td><td>${event.nb_inscriptions}</td></tr>`
                        ).join('')}
                    </tbody>
                </table>
            </div>
            
            <div class="footer">
                <p>Rapport généré automatiquement par EcoMind - Plateforme de Gestion d'Événements Écologiques</p>
            </div>
        </body>
        </html>
    `;
    
    // Open print dialog with the formatted content
    const printWindow = window.open('', '_blank');
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    
    // Wait for content to load then trigger print
    printWindow.onload = function() {
        printWindow.print();
        // Close the window after printing (optional)
        setTimeout(function() {
            printWindow.close();
        }, 1000);
    };
}
</script>

</body>
</html>