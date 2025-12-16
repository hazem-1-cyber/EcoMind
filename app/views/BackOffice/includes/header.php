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
        if (!isset($donsPending)) {
            require_once __DIR__ . '/../../controller/DonController.php';
            $donCtrl = new DonController();
            $allDons = $donCtrl->listDons()->fetchAll();
            $donsPending = 0;
            foreach ($allDons as $don) {
                if ($don['statut'] === 'pending') {
                    $donsPending++;
                }
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

<script>
document.addEventListener('DOMContentLoaded', () => {
    const notificationBtn = document.getElementById('notificationBtn');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const closeNotification = document.getElementById('closeNotification');
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    // Toggle sidebar
    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
    
    // Notification dropdown
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
});
</script>
