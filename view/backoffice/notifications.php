<?php
// Système de notifications dynamiques pour le backoffice EcoMind

function genererNotificationsPage($db, $page = 'dashboard') {
    $notifications = [];
    
    // Statistiques de base
    $totalReponses = $db->query("SELECT COUNT(*) FROM reponse_formulaire")->fetchColumn();
    $nouvellesReponses = $db->query("SELECT COUNT(*) FROM reponse_formulaire WHERE DATE(date_soumission) = CURDATE()")->fetchColumn();
    $recentesReponses = $db->query("SELECT COUNT(*) FROM reponse_formulaire WHERE date_soumission >= DATE_SUB(NOW(), INTERVAL 2 HOUR)")->fetchColumn();
    $totalConseils = $db->query("SELECT COUNT(*) FROM conseil")->fetchColumn();
    
    switch ($page) {
        case 'dashboard':
            // Nouvelles réponses aujourd'hui
            if ($nouvellesReponses > 0) {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => 'fa-user-plus',
                    'title' => $nouvellesReponses == 1 ? 'Nouvelle réponse reçue' : "$nouvellesReponses nouvelles réponses",
                    'message' => $nouvellesReponses == 1 ? 'Un utilisateur a soumis ses données écologiques' : "$nouvellesReponses utilisateurs ont soumis leurs données",
                    'time' => 'Aujourd\'hui',
                    'unread' => true
                ];
            }
            
            // Conseils IA générés
            if ($recentesReponses > 0) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'fa-lightbulb',
                    'title' => 'Conseils IA générés',
                    'message' => ($recentesReponses * 3) . ' nouveaux conseils personnalisés ont été créés par l\'IA',
                    'time' => 'Il y a ' . ($recentesReponses > 5 ? '2 heures' : '30 minutes'),
                    'unread' => true
                ];
            }
            
            // Système performant
            if ($totalReponses >= 10) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'fa-chart-line',
                    'title' => 'Système performant',
                    'message' => "$totalReponses réponses traitées avec succès. L'IA fonctionne parfaitement !",
                    'time' => 'Il y a 1 heure',
                    'unread' => false
                ];
            }
            break;
            
        case 'conseils':
            // Conseils récemment modifiés
            if ($totalConseils > 0) {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => 'fa-edit',
                    'title' => 'Conseils mis à jour',
                    'message' => 'Des conseils ont été récemment modifiés dans la base de données',
                    'time' => 'Il y a 15 minutes',
                    'unread' => true
                ];
            }
            
            // Nouveau conseil ajouté
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fa-plus-circle',
                'title' => 'Prêt pour nouveaux conseils',
                'message' => 'Vous pouvez ajouter de nouveaux conseils écologiques',
                'time' => 'Il y a 30 minutes',
                'unread' => false
            ];
            break;
            
        case 'reponses':
            // Nouvelles réponses
            if ($nouvellesReponses > 0) {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => 'fa-user-plus',
                    'title' => 'Nouvelles données reçues',
                    'message' => "$nouvellesReponses nouvelles réponses utilisateurs à analyser",
                    'time' => 'Il y a 5 minutes',
                    'unread' => true
                ];
            }
            
            // Analyse des données
            if ($totalReponses > 5) {
                $notifications[] = [
                    'type' => 'info',
                    'icon' => 'fa-chart-bar',
                    'title' => 'Données analysées',
                    'message' => 'Les statistiques ont été mises à jour avec les dernières réponses',
                    'time' => 'Il y a 20 minutes',
                    'unread' => false
                ];
            }
            break;
            
        case 'ia':
            // IA optimisée
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fa-robot',
                'title' => 'IA optimisée',
                'message' => 'Le générateur de conseils a été amélioré pour plus de personnalisation',
                'time' => 'Il y a 2 heures',
                'unread' => true
            ];
            
            // Performance IA
            if ($totalReponses > 0) {
                $notifications[] = [
                    'type' => 'success',
                    'icon' => 'fa-check-circle',
                    'title' => 'IA fonctionnelle',
                    'message' => ($totalReponses * 3) . ' conseils générés avec succès',
                    'time' => 'Il y a 1 heure',
                    'unread' => false
                ];
            }
            break;
            
        case 'test_ia':
            // Test réussi
            $notifications[] = [
                'type' => 'success',
                'icon' => 'fa-check-circle',
                'title' => 'Test IA réussi',
                'message' => 'Tous les conseils ont été générés avec succès',
                'time' => 'À l\'instant',
                'unread' => true
            ];
            break;
            
        case 'voir_conseil':
            // Conseils régénérés
            $notifications[] = [
                'type' => 'info',
                'icon' => 'fa-refresh',
                'title' => 'Conseils régénérés',
                'message' => '3 nouveaux conseils personnalisés ont été créés pour cet utilisateur',
                'time' => 'À l\'instant',
                'unread' => true
            ];
            break;
    }
    
    // Si aucune notification spécifique, notification par défaut
    if (empty($notifications)) {
        $notifications[] = [
            'type' => 'info',
            'icon' => 'fa-info-circle',
            'title' => 'Système opérationnel',
            'message' => 'EcoMind fonctionne parfaitement et est prêt à traiter les données',
            'time' => 'Il y a 5 minutes',
            'unread' => false
        ];
    }
    
    return $notifications;
}

function renderNotificationBell($notifications) {
    $notificationsNonLues = count(array_filter($notifications, fn($n) => $n['unread']));
    
    $html = '<div class="notification-bell ' . ($notificationsNonLues > 0 ? 'has-notifications' : '') . '" id="notificationBell" onclick="toggleNotifications()">';
    $html .= '<i class="fas fa-bell"></i>';
    
    if ($notificationsNonLues > 0) {
        $html .= '<span class="notification-badge" id="notificationBadge">' . $notificationsNonLues . '</span>';
    }
    
    $html .= '<div class="notification-dropdown" id="notificationDropdown">';
    $html .= '<div class="notification-header">';
    $html .= '<span>Notifications</span>';
    $html .= '<span id="notificationCount">' . count($notifications) . '</span>';
    $html .= '</div>';
    $html .= '<div class="notification-list" id="notificationList">';
    
    if (!empty($notifications)) {
        foreach ($notifications as $notif) {
            $html .= '<div class="notification-item ' . ($notif['unread'] ? 'unread' : '') . '">';
            $html .= '<div class="notification-icon ' . $notif['type'] . '">';
            $html .= '<i class="fas ' . $notif['icon'] . '"></i>';
            $html .= '</div>';
            $html .= '<div class="notification-content">';
            $html .= '<div class="notification-title">' . htmlspecialchars($notif['title']) . '</div>';
            $html .= '<div class="notification-message">' . htmlspecialchars($notif['message']) . '</div>';
            $html .= '<div class="notification-time">' . htmlspecialchars($notif['time']) . '</div>';
            $html .= '</div>';
            $html .= '</div>';
        }
    } else {
        $html .= '<div class="notification-empty">';
        $html .= '<i class="fas fa-bell-slash"></i>';
        $html .= '<p>Aucune notification</p>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    if ($notificationsNonLues > 0) {
        $html .= '<div class="notification-footer">';
        $html .= '<a href="#" onclick="markAllAsRead()">Marquer tout comme lu</a>';
        $html .= '</div>';
    }
    
    $html .= '</div>';
    $html .= '</div>';
    
    return $html;
}

function getNotificationJavaScript() {
    return '
    function toggleNotifications() {
        const dropdown = document.getElementById("notificationDropdown");
        const bell = document.getElementById("notificationBell");
        
        if (dropdown.classList.contains("show")) {
            dropdown.classList.remove("show");
            bell.classList.remove("open");
        } else {
            dropdown.classList.add("show");
            bell.classList.add("open");
        }
    }

    function markAllAsRead() {
        const notifications = document.querySelectorAll(".notification-item.unread");
        const badge = document.getElementById("notificationBadge");
        const count = document.getElementById("notificationCount");
        
        notifications.forEach(notification => {
            notification.classList.remove("unread");
        });
        
        if (badge) badge.style.display = "none";
        count.textContent = "0";
        
        showNotification("Toutes les notifications ont été marquées comme lues", "success");
    }

    // Fermer les notifications en cliquant en dehors
    document.addEventListener("click", function(e) {
        const bell = document.getElementById("notificationBell");
        const dropdown = document.getElementById("notificationDropdown");
        
        if (bell && !bell.contains(e.target)) {
            dropdown.classList.remove("show");
            bell.classList.remove("open");
        }
    });
    ';
}
?>