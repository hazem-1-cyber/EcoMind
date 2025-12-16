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
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        <a href="dons.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dons.php' ? 'active' : '' ?>">
            <i class="fas fa-hand-holding-heart"></i>
            <span>Tous les dons</span>
        </a>
        <a href="lisdon.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'lisdon.php' ? 'active' : '' ?>">
            <i class="fas fa-list"></i>
            <span>Gestion des dons</span>
        </a>
        <a href="listcategorie.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'listcategorie.php' ? 'active' : '' ?>">
            <i class="fas fa-tags"></i>
            <span>Catégories</span>
        </a>
        <a href="associations.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'associations.php' ? 'active' : '' ?>">
            <i class="fas fa-building"></i>
            <span>Associations</span>
        </a>
        <a href="statistiques.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'statistiques.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-pie"></i>
            <span>Statistiques</span>
        </a>
        <a href="parametres.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'parametres.php' ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Paramètres</span>
        </a>
        <a href="#" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </nav>
</aside>
