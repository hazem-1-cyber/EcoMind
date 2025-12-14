<?php
require_once __DIR__ . "/../../controller/DonController.php";

$donCtrl = new DonController();

// Traitement des actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $donId = $_POST['don_id'] ?? '';
    
    switch ($action) {
        case 'restaurer':
            if ($donCtrl->restaurerDon($donId)) {
                $message = "Don restauré avec succès !";
                $messageType = "success";
            } else {
                $message = "Erreur lors de la restauration du don.";
                $messageType = "error";
            }
            break;
            
        case 'supprimer_definitif':
            if ($donCtrl->supprimerDefinitivement($donId)) {
                $message = "Don supprimé définitivement !";
                $messageType = "success";
            } else {
                $message = "Erreur lors de la suppression définitive.";
                $messageType = "error";
            }
            break;
            
        case 'vider_corbeille':
            if ($donCtrl->viderCorbeille()) {
                $message = "Corbeille vidée avec succès !";
                $messageType = "success";
            } else {
                $message = "Erreur lors du vidage de la corbeille.";
                $messageType = "error";
            }
            break;
    }
}

// Récupérer les dons de la corbeille
$donsCorbeille = $donCtrl->listDonsCorbeille();
$statsCorbeille = $donCtrl->getStatsCorbeille();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Corbeille - EcoMind Admin</title>
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
            <a href="corbeille.php" class="nav-item active">
                <i class="fas fa-trash-alt"></i>
                <span>Corbeille</span>
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
            <a href="statistiques.php" class="nav-item">
                <i class="fas fa-chart-pie"></i>
                <span>Statistiques</span>
            </a>
            <a href="parametres.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Paramètres</span>
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
        </header>

        <!-- Content Area -->
        <div class="container">
            <!-- Messages -->
            <?php if (isset($message)): ?>
                <div class="alert alert-<?= $messageType === 'success' ? 'success' : 'danger' ?>" style="margin-bottom: 20px;">
                    <i class="fas fa-<?= $messageType === 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <h1 class="page-title">
                <i class="fas fa-trash-alt" style="color: #dc3545; margin-right: 10px;"></i>
                Corbeille
            </h1>
            <p class="page-subtitle">Gérer les dons supprimés - Restaurer ou supprimer définitivement</p>

            <!-- Statistiques de la corbeille -->
            <div class="stats-overview" style="margin: 30px 0; display: flex; justify-content: space-between; align-items: center;">
                <div class="stat-card" style="flex: 0 0 auto;">
                    <div class="stat-icon" style="background: linear-gradient(135deg, #dc3545, #c82333);">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3><?= $statsCorbeille['total_corbeille'] ?></h3>
                        <p>Total dans la corbeille</p>
                    </div>
                </div>
                
                <?php if ($statsCorbeille['total_corbeille'] > 0): ?>
                <div class="action-buttons">
                    <form method="POST" style="display: inline;" onsubmit="return confirm('⚠️ ATTENTION !\n\nÊtes-vous absolument sûr de vouloir vider complètement la corbeille ?\n\nCette action supprimera définitivement tous les dons et ne peut pas être annulée.')">
                        <input type="hidden" name="action" value="vider_corbeille">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Vider la corbeille
                        </button>
                    </form>
                </div>
                <?php endif; ?>
            </div>



            <!-- Table des dons dans la corbeille -->
            <div class="table-section">
                <div class="section-header">
                    <h2><i class="fas fa-list"></i> Dons dans la corbeille (<?= count($donsCorbeille) ?>)</h2>
                </div>

                <?php if (empty($donsCorbeille)): ?>
                    <div class="empty-state" style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <i class="fas fa-trash-alt" style="font-size: 64px; color: #dc3545; margin-bottom: 20px; opacity: 0.5;"></i>
                        <h3 style="color: #013220; margin-bottom: 10px;">Corbeille vide</h3>
                        <p style="color: #6c757d; margin-bottom: 30px;">Aucun don dans la corbeille pour le moment.</p>
                        <a href="lisdon.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i>
                            Retour à la gestion des dons
                        </a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Email</th>
                                    <th>Montant</th>
                                    <th>Date suppression</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($donsCorbeille as $don): ?>
                                <tr>
                                    <td><strong>#<?= $don['id'] ?></strong></td>
                                    <td>
                                        <span class="type-badge type-<?= $don['type_don'] ?>">
                                            <i class="fas fa-<?= $don['type_don'] === 'money' ? 'coins' : 'box' ?>"></i>
                                            <?= ucfirst($don['type_don']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($don['email']) ?></td>
                                    <td>
                                        <?php if ($don['type_don'] === 'money'): ?>
                                            <strong style="color: #28a745;"><?= number_format($don['montant'], 2) ?> TND</strong>
                                        <?php else: ?>
                                            <span style="color: #6c757d;">Matériel</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($don['deleted_at'])) ?></td>
                                    <td>
                                        <span class="status-badge status-deleted">
                                            <i class="fas fa-trash"></i>
                                            Supprimé
                                        </span>
                                    </td>
                                    <td class="table-actions">
                                        <!-- Restaurer -->
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Restaurer ce don ?')">
                                            <input type="hidden" name="action" value="restaurer">
                                            <input type="hidden" name="don_id" value="<?= $don['id'] ?>">
                                            <button type="submit" class="btn-icon btn-success" title="Restaurer">
                                                <i class="fas fa-undo"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Supprimer définitivement -->
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Supprimer définitivement ce don ? Cette action est irréversible.')">
                                            <input type="hidden" name="action" value="supprimer_definitif">
                                            <input type="hidden" name="don_id" value="<?= $don['id'] ?>">
                                            <button type="submit" class="btn-icon btn-danger" title="Supprimer définitivement">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Styles personnalisés pour la corbeille -->
<style>
.alert {
    padding: 15px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-danger {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.stats-grid {
    display: flex;
    gap: 20px;
}

.stat-card {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 20px;
}

.stat-content h3 {
    margin: 0 0 5px 0;
    font-size: 24px;
    font-weight: 700;
    color: #013220;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-size: 14px;
}

.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

.type-money {
    background: rgba(255, 193, 7, 0.1);
    color: #856404;
}

.type-material {
    background: rgba(108, 117, 125, 0.1);
    color: #495057;
}

.status-deleted {
    background: #dc3545;
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-icon {
    width: 32px;
    height: 32px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    margin: 0 2px;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
    transform: scale(1.1);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
    transform: scale(1.1);
}

.btn-primary {
    background: linear-gradient(135deg, #013220, #0B3D2E);
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(1, 50, 32, 0.3);
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
    border: 2px solid transparent;
}

.btn-danger:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(220, 53, 69, 0.3);
    background: linear-gradient(135deg, #c82333, #a71e2a);
}

.stats-overview {
    background: white;
    padding: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}
</style>

<script>
    // Toggle sidebar on mobile
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    
    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Auto-hide messages after 5 seconds
    const messages = document.querySelectorAll('.alert');
    messages.forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });

    // Confirmation améliorée pour les actions
    document.querySelectorAll('form[onsubmit]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const action = this.querySelector('input[name="action"]').value;
            let message = '';
            
            switch(action) {
                case 'restaurer':
                    message = 'Êtes-vous sûr de vouloir restaurer ce don ? Il sera remis en attente de validation.';
                    break;
                case 'supprimer_definitif':
                    message = 'ATTENTION : Cette action est irréversible !\n\nÊtes-vous absolument sûr de vouloir supprimer définitivement ce don ?';
                    break;
                case 'vider_corbeille':
                    message = 'ATTENTION : Cette action est irréversible !\n\nÊtes-vous sûr de vouloir vider complètement la corbeille ?\nTous les dons seront supprimés définitivement.';
                    break;
            }
            
            if (message && !confirm(message)) {
                e.preventDefault();
            }
        });
    });
</script>

</body>
</html>