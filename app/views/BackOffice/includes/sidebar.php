<?php
// S'assurer que la session est démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

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
        <!-- Dashboard avec statistiques users -->
        <a href="dashboard.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Dashboard</span>
        </a>
        
        <!-- Event - Travail du camarade (vide pour l'instant) -->
        <a href="#" class="nav-item" onclick="alert('Section Event - Travail en cours par votre camarade')">
            <i class="fas fa-calendar-alt"></i>
            <span>Event</span>
        </a>
        
        <!-- Shop - Travail du camarade (vide pour l'instant) -->
        <a href="#" class="nav-item" onclick="alert('Section Shop - Travail en cours par votre camarade')">
            <i class="fas fa-shopping-cart"></i>
            <span>Shop</span>
        </a>
        
        <!-- Don - Travail du camarade (vide pour l'instant) -->
        <a href="#" class="nav-item" onclick="alert('Section Don - Travail en cours par votre camarade')">
            <i class="fas fa-heart"></i>
            <span>Don</span>
        </a>
        
        <!-- Déconnexion -->
        <a href="logout.php" class="nav-item">
            <i class="fas fa-sign-out-alt"></i>
            <span>Déconnexion</span>
        </a>
    </nav>
</aside>
