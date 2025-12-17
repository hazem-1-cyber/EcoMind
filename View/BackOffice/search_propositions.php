<?php
// search_propositions.php - Recherche avancée des propositions
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - Recherche Propositions</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css?v=<?= date('YmdHis') . rand(1000,9999) ?>">
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
            <a href="<?= BASE_URL ?>/index.php?page=statistiques" class="nav-item">
                <i class="fas fa-chart-bar"></i>
                <span>Statistiques</span>
            </a>
            <div class="nav-dropdown active" id="searchDropdown">
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
                    <a href="<?= BASE_URL ?>/index.php?page=search_propositions" class="nav-sub-item active">
                        <i class="fas fa-lightbulb"></i>
                        <span>Propositions</span>
                    </a>
                </div>
            </div>
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
                <h1><i class="fas fa-search"></i> Recherche Avancée - Propositions</h1>
            </div>
            <div class="header-right">
                <a href="<?= BASE_URL ?>/index.php?page=admin_propositions&add=1" class="header-icon" title="Ajouter une proposition">
                    <i class="fas fa-plus"></i>
                </a>
            </div>
        </div>

        <!-- SEARCH CONTAINER -->
        <div id="searchContainer"></div>
    </div>
</div>

<script>
// Configuration
const BASE_URL = '<?= BASE_URL ?>';

// Initialize Advanced Search
document.addEventListener('DOMContentLoaded', function() {
    window.advancedSearch = new AdvancedSearch({
        container: '#searchContainer',
        type: 'propositions',
        apiUrl: BASE_URL + '/api/search_simple.php',
        baseUrl: BASE_URL
    });
    
    // Navigation functionality
    document.querySelectorAll('[data-section]').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.dataset.section;
            window.location.href = BASE_URL + '/index.php?page=admin_dashboard#' + section;
        });
    });
    
    // Search dropdown functionality - keep it always active on search pages
    const searchDropdown = document.getElementById('searchDropdown');
    if (searchDropdown) {
        searchDropdown.addEventListener('click', function(e) {
            if (e.target.closest('.nav-dropdown-toggle')) {
                e.preventDefault();
                // Keep dropdown active on search pages
                this.classList.add('active');
            }
        });
        
        // Ensure dropdown is always open on search pages
        searchDropdown.classList.add('active');
    }
});
</script>

<script src="<?= BASE_URL ?>/assets/js/advanced-search.js?v=<?= date('YmdHis') . rand(1000,9999) ?>"></script>

</body>
</html>