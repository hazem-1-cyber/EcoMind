<?php
// dashboard.php
// Calculate stats
$totalEvents = count($events);
$totalInscriptions = count($inscriptions);
$totalPropositions = count($propositions);

// Count today's inscriptions
$today = date('Y-m-d');
$inscriptionsToday = 0;
foreach ($inscriptions as $ins) {
    if (strpos($ins['date_inscription'], $today) === 0) {
        $inscriptionsToday++;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - Backoffice Événements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
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
            <a href="#" class="nav-item active" data-section="dashboard">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="#" class="nav-item" data-section="events">
                <i class="fas fa-calendar-alt"></i>
                <span>Événements</span>
            </a>
            <a href="#" class="nav-item" data-section="inscriptions">
                <i class="fas fa-users"></i>
                <span>Inscriptions</span>
            </a>
            <a href="#" class="nav-item" data-section="propositions">
                <i class="fas fa-lightbulb"></i>
                <span>Propositions</span>
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
                <h1>Gestion des Événements</h1>
            </div>
            <div class="header-right">
                <div class="header-icon notification-icon" id="notificationBtn">
                    <i class="fas fa-bell"></i>
                    <?php 
                    $totalNotifications = $inscriptionsToday + (count($propositions) > 0 ? 1 : 0);
                    if ($totalNotifications > 0): 
                    ?>
                    <span class="badge"><?= $totalNotifications ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Notification Dropdown -->
                <div class="notification-dropdown" id="notificationDropdown">
                    <div class="notification-header">
                        <h3>Notifications</h3>
                        <span class="notification-count"><?= $totalNotifications ?> nouvelles</span>
                    </div>
                    <div class="notification-list">
                        <?php if ($inscriptionsToday > 0): ?>
                        <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="notification-item" onclick="document.querySelector('[data-section=inscriptions]').click(); document.getElementById('notificationDropdown').classList.remove('active');">
                            <div class="notification-icon-wrapper">
                                <i class="fas fa-user-plus"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-title"><?= $inscriptionsToday ?> nouvelle(s) inscription(s)</p>
                                <p class="notification-time">Aujourd'hui</p>
                            </div>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (count($propositions) > 0): ?>
                        <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="notification-item" onclick="document.querySelector('[data-section=propositions]').click(); document.getElementById('notificationDropdown').classList.remove('active');">
                            <div class="notification-icon-wrapper">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div class="notification-content">
                                <p class="notification-title"><?= count($propositions) ?> proposition(s) en attente</p>
                                <p class="notification-time">À traiter</p>
                            </div>
                        </a>
                        <?php endif; ?>
                        
                        <?php if ($totalNotifications === 0): ?>
                        <div class="notification-empty">
                            <i class="fas fa-check-circle"></i>
                            <p>Aucune nouvelle notification</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="notification-footer">
                        <a href="#propositions" onclick="document.querySelector('[data-section=propositions]').click()">Voir toutes les propositions</a>
                    </div>
                </div>
                
                <div class="header-icon user-icon" id="userBtn">
                    <i class="fas fa-user"></i>
                </div>
            </div>
        </div>

        <!-- DASHBOARD SECTION -->
        <div class="content-section active" id="dashboard-section">
            <!-- STATS GRID -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $totalEvents ?></h3>
                        <p>Total Événements</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $totalInscriptions ?></h3>
                        <p>Total Inscriptions</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $inscriptionsToday ?></h3>
                        <p>Inscriptions Aujourd'hui</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-lightbulb"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $totalPropositions ?></h3>
                        <p>Propositions Reçues</p>
                    </div>
                </div>
            </div>

            <!-- OVERVIEW -->
            <div style="background: rgba(255, 255, 255, 0.95); padding: 30px; border-radius: 20px; margin-top: 30px; box-shadow: 0 8px 30px rgba(1, 50, 32, 0.08);">
                <h2 style="color: #013220; margin-bottom: 20px;">Vue d'ensemble</h2>
                <p style="font-size: 16px; color: #666; line-height: 1.8;">
                    Bienvenue sur le backoffice de gestion des événements EcoMind ! 
                    Vous avez actuellement <strong><?= $totalEvents ?></strong> événements actifs, 
                    <strong><?= $totalInscriptions ?></strong> inscriptions enregistrées et 
                    <strong><?= $totalPropositions ?></strong> propositions en attente.
                    Utilisez le menu de gauche pour naviguer entre les différentes sections.
                </p>
            </div>
        </div>

        <!-- EVENTS SECTION -->
        <div class="content-section" id="events-section">
            <main class="admin-main">
                <h2>Back-office : Événements</h2>
                
                <div class="admin-actions">
                    <a href="<?= BASE_URL ?>/index.php?page=admin_events&add=1" class="btn-admin btn-add">+ Ajouter un événement</a>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Type</th>
                            <th>Date création</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($events as $e): ?>
                        <tr>
                            <td><?= $e['id'] ?></td>
                            <td><?= htmlspecialchars($e['titre']) ?></td>
                            <td><?= htmlspecialchars($e['type']) ?></td>
                            <td><?= $e['date_creation'] ?></td>
                            <td class="actions">
                                <a href="<?= BASE_URL ?>/index.php?page=admin_events&edit=<?= $e['id'] ?>" class="btn-edit">Modifier</a>
                                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard&delete_event=<?= $e['id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>

        <!-- INSCRIPTIONS SECTION -->
        <div class="content-section" id="inscriptions-section">
            <main class="admin-main">
                <h2>Back-office : Inscriptions</h2>
                
                <div class="admin-actions">
                    <a href="<?= BASE_URL ?>/index.php?page=admin_inscriptions&add=1" class="btn-admin btn-add">+ Ajouter une inscription</a>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Événement ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Âge</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($inscriptions as $ins): ?>
                        <tr>
                            <td><?= $ins['id'] ?></td>
                            <td><?= $ins['evenement_id'] ?></td>
                            <td><?= htmlspecialchars($ins['nom']) ?></td>
                            <td><?= htmlspecialchars($ins['prenom']) ?></td>
                            <td><?= $ins['age'] ?></td>
                            <td><?= htmlspecialchars($ins['email']) ?></td>
                            <td><?= htmlspecialchars($ins['tel']) ?></td>
                            <td><?= $ins['date_inscription'] ?></td>
                            <td class="actions">
                                <a href="<?= BASE_URL ?>/index.php?page=admin_inscriptions&edit=<?= $ins['id'] ?>" class="btn-edit">Modifier</a>
                                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard&delete_inscription=<?= $ins['id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>

        <!-- PROPOSITIONS SECTION -->
        <div class="content-section" id="propositions-section">
            <main class="admin-main">
                <h2>Back-office : Propositions</h2>
                
                <div class="admin-actions">
                    <a href="<?= BASE_URL ?>/index.php?page=admin_propositions&add=1" class="btn-admin btn-add">+ Ajouter une proposition</a>
                </div>

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Association</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($propositions as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= htmlspecialchars($p['association_nom']) ?></td>
                            <td><?= htmlspecialchars($p['email_contact']) ?></td>
                            <td><?= htmlspecialchars($p['tel']) ?></td>
                            <td><?= htmlspecialchars($p['type']) ?></td>
                            <td><?= htmlspecialchars(substr($p['description'], 0, 50)) ?>...</td>
                            <td><?= $p['date_proposition'] ?></td>
                            <td class="actions">
                                <a href="<?= BASE_URL ?>/index.php?page=admin_propositions&edit=<?= $p['id'] ?>" class="btn-edit">Modifier</a>
                                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard&delete_proposition=<?= $p['id'] ?>" 
                                   class="btn-delete" 
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette proposition ?')">
                                   Supprimer
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</div>

<script>
// Navigation sidebar
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item[data-section]');
    const sections = document.querySelectorAll('.content-section');

    // Function to show a specific section
    function showSection(sectionName) {
        // Remove active class from all items
        navItems.forEach(nav => nav.classList.remove('active'));
        
        // Add active class to clicked item
        const activeItem = document.querySelector(`.nav-item[data-section="${sectionName}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
        }

        // Hide all sections
        sections.forEach(section => section.classList.remove('active'));

        // Show target section
        const target = document.getElementById(sectionName + '-section');
        if (target) {
            target.classList.add('active');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }

    // Handle hash on page load
    if (window.location.hash) {
        const hash = window.location.hash.substring(1); // Remove #
        showSection(hash);
    }

    // Handle sidebar clicks
    navItems.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const targetSection = this.getAttribute('data-section');
            showSection(targetSection);
            // Update URL hash
            window.location.hash = targetSection;
        });
    });

    // Notification dropdown
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    
    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!notificationDropdown.contains(e.target) && !notificationBtn.contains(e.target)) {
                notificationDropdown.classList.remove('active');
            }
        });
    }

    // Auto-update notifications
    function updateNotifications() {
        fetch('<?= BASE_URL ?>/api/notifications.php')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update badge
                    const badge = document.querySelector('.notification-icon .badge');
                    if (data.totalNotifications > 0) {
                        if (badge) {
                            badge.textContent = data.totalNotifications;
                        } else {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge';
                            newBadge.textContent = data.totalNotifications;
                            notificationBtn.appendChild(newBadge);
                        }
                    } else if (badge) {
                        badge.remove();
                    }

                    // Update notification count in header
                    const notifCount = document.querySelector('.notification-count');
                    if (notifCount) {
                        notifCount.textContent = data.totalNotifications + ' nouvelles';
                    }

                    // Update notification list
                    const notifList = document.querySelector('.notification-list');
                    if (notifList) {
                        let html = '';
                        
                        if (data.inscriptionsToday > 0) {
                            html += `
                                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="notification-item" onclick="document.querySelector('[data-section=inscriptions]').click(); document.getElementById('notificationDropdown').classList.remove('active');">
                                    <div class="notification-icon-wrapper">
                                        <i class="fas fa-user-plus"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-title">${data.inscriptionsToday} nouvelle(s) inscription(s)</p>
                                        <p class="notification-time">Aujourd'hui</p>
                                    </div>
                                </a>
                            `;
                        }
                        
                        if (data.totalPropositions > 0) {
                            html += `
                                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="notification-item" onclick="document.querySelector('[data-section=propositions]').click(); document.getElementById('notificationDropdown').classList.remove('active');">
                                    <div class="notification-icon-wrapper">
                                        <i class="fas fa-lightbulb"></i>
                                    </div>
                                    <div class="notification-content">
                                        <p class="notification-title">${data.totalPropositions} proposition(s) en attente</p>
                                        <p class="notification-time">À traiter</p>
                                    </div>
                                </a>
                            `;
                        }
                        
                        if (data.totalNotifications === 0) {
                            html = `
                                <div class="notification-empty">
                                    <i class="fas fa-check-circle"></i>
                                    <p>Aucune nouvelle notification</p>
                                </div>
                            `;
                        }
                        
                        notifList.innerHTML = html;
                    }
                }
            })
            .catch(error => console.error('Error updating notifications:', error));
    }

    // Update notifications every 3 seconds for near real-time updates
    setInterval(updateNotifications, 3000);
});
</script>

</body>
</html>
