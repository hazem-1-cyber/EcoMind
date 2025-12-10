<?php
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

require_once '../../config.php';
require_once 'notifications.php';
$db = Config::getConnexion();
$message = '';

// === SUPPRESSION RÉPONSE ===
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM reponse_formulaire WHERE idformulaire = ?")->execute([$id]);
    $_SESSION['notification'] = 'Réponse supprimée avec succès !';
    header("Location: gestion_reponses.php");
    exit;
}

// === AJOUT RÉPONSE ===
if (isset($_POST['action']) && $_POST['action'] === 'add_reponse') {
    $email = trim($_POST['email'] ?? '');
    $nb_personnes = (int)($_POST['nb_personnes'] ?? 0);
    $douche_freq = (int)($_POST['douche_freq'] ?? 0);
    $douche_duree = (int)($_POST['douche_duree'] ?? 10);
    $chauffage = trim($_POST['chauffage'] ?? '');
    $temp_hiver = (int)($_POST['temp_hiver'] ?? 20);
    $transport = trim($_POST['transport_travail'] ?? '');
    $distance_travail = (int)($_POST['distance_travail'] ?? 0);
    
    if ($nb_personnes > 0 && $douche_freq > 0 && $chauffage && $transport) {
        $stmt = $db->prepare("INSERT INTO reponse_formulaire (email, nb_personnes, douche_freq, douche_duree, chauffage, temp_hiver, transport_travail, distance_travail, date_soumission) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$email, $nb_personnes, $douche_freq, $douche_duree, $chauffage, $temp_hiver, $transport, $distance_travail]);
        $_SESSION['notification'] = 'Réponse ajoutée avec succès !';
        header("Location: gestion_reponses.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Les champs obligatoires doivent être remplis.';
        $_SESSION['notification_type'] = 'error';
        header("Location: gestion_reponses.php");
        exit;
    }
}

// === MODIFICATION RÉPONSE ===
if (isset($_POST['action']) && $_POST['action'] === 'update_reponse') {
    $id = (int)$_POST['idformulaire'];
    $email = trim($_POST['email'] ?? '');
    $nb_personnes = (int)($_POST['nb_personnes'] ?? 0);
    $douche_freq = (int)($_POST['douche_freq'] ?? 0);
    $douche_duree = (int)($_POST['douche_duree'] ?? 10);
    $chauffage = trim($_POST['chauffage'] ?? '');
    $temp_hiver = (int)($_POST['temp_hiver'] ?? 20);
    $transport = trim($_POST['transport_travail'] ?? '');
    $distance_travail = (int)($_POST['distance_travail'] ?? 0);
    
    if ($id && $nb_personnes > 0 && $douche_freq > 0 && $chauffage && $transport) {
        $stmt = $db->prepare("UPDATE reponse_formulaire SET email = ?, nb_personnes = ?, douche_freq = ?, douche_duree = ?, chauffage = ?, temp_hiver = ?, transport_travail = ?, distance_travail = ? WHERE idformulaire = ?");
        $stmt->execute([$email, $nb_personnes, $douche_freq, $douche_duree, $chauffage, $temp_hiver, $transport, $distance_travail, $id]);
        $_SESSION['notification'] = 'Réponse modifiée avec succès !';
        header("Location: gestion_reponses.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Erreur lors de la modification.';
        $_SESSION['notification_type'] = 'error';
        header("Location: gestion_reponses.php");
        exit;
    }
}

// === STATS ===
$totalReponses = $db->query("SELECT COUNT(*) FROM reponse_formulaire")->fetchColumn();
$aujourdhui = $db->query("SELECT COUNT(*) FROM reponse_formulaire WHERE DATE(date_soumission) = CURDATE()")->fetchColumn();
$totalPersonnes = $db->query("SELECT SUM(nb_personnes) FROM reponse_formulaire")->fetchColumn() ?: 0;
$moyennePersonnes = $totalReponses > 0 ? round($totalPersonnes / $totalReponses, 1) : 0;

// Récupération de toutes les réponses
$reponses = $db->query("SELECT * FROM reponse_formulaire ORDER BY date_soumission DESC")->fetchAll(PDO::FETCH_OBJ);

// Générer les notifications pour cette page
$notifications = genererNotificationsPage($db, 'reponses');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Réponses - EcoMind</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/backoffice.css?v=<?php echo time(); ?>">
</head>
<body>

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">
                    <img src="../frontoffice/assets/images/Screenshot_2025-11-16_152042-removebg-preview.png" alt="EcoMind Logo">
                </div>
                <span class="logo-text">EcoMind</span>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="index.php" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="gerer_conseils.php" class="nav-item">
                <i class="fas fa-cog"></i>
                <span>Gestion de Conseils</span>
            </a>
            <a href="gestion_reponses.php" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Gestion des Réponses</span>
            </a>
            <a href="gestion_ia.php" class="nav-item">
                <span>🤖 Gestion IA</span>
            </a>
            <a href="test_ia.php" class="nav-item">
                <i class="fas fa-flask"></i>
                <span>Test IA</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-header">
            <h1><i class="fas fa-users"></i> Gestion des Réponses</h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <!-- Cloche de notifications -->
                <?= renderNotificationBell($notifications) ?>
                <a href="index.php" class="logout-btn">Retour au Dashboard</a>
            </div>
        </div>

        <!-- STATISTIQUES -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-poll"></i></div>
                <div>
                    <p>Total Réponses</p>
                    <h3><?= $totalReponses ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <div>
                    <p>Total Personnes</p>
                    <h3><?= $totalPersonnes ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
                <div>
                    <p>Aujourd'hui</p>
                    <h3><?= $aujourdhui ?></h3>
                </div>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-chart-bar"></i></div>
                <div>
                    <p>Moyenne Personnes/Foyer</p>
                    <h3><?= $moyennePersonnes ?></h3>
                </div>
            </div>
        </div>

        <!-- AJOUT NOUVELLE RÉPONSE -->
        <div class="section" style="background: linear-gradient(135deg, rgba(168, 230, 207, 0.1), rgba(1, 50, 32, 0.05)); border-left: 4px solid #A8E6CF; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin: 0; color: #013220;"><i class="fas fa-plus-circle" style="color: #A8E6CF;"></i> Ajouter une nouvelle réponse</h2>
                <button type="button" id="toggle-form-btn" class="btn" style="background: #A8E6CF; color: #013220; padding: 8px 15px; border-radius: 20px; border: none; cursor: pointer; font-weight: bold; transition: all 0.3s;">
                    <i class="fas fa-chevron-down"></i> Afficher le formulaire
                </button>
            </div>
            
            <div id="add-form-container" style="display: none; animation: slideDown 0.3s ease;">
                <form method="post" id="form-add-reponse" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <input type="hidden" name="action" value="add_reponse">
                    
                    <!-- Section Informations générales -->
                    <div class="form-section">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #A8E6CF;">
                            <i class="fas fa-user"></i> Informations générales
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="email">📧 Email (optionnel)</label>
                                <input type="text" name="email" id="email" placeholder="exemple@email.com">
                                <span class="error-message" id="error-email"></span>
                                <span class="success-message" id="success-email"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="nb_personnes">👥 Nombre de personnes dans le foyer *</label>
                                <input type="text" name="nb_personnes" id="nb_personnes" placeholder="Ex: 4">
                                <span class="error-message" id="error-nb_personnes"></span>
                                <span class="success-message" id="success-nb_personnes"></span>
                                <small class="form-help">Entre 1 et 20 personnes</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section Consommation d'eau -->
                    <div class="form-section">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #4FC3F7;">
                            <i class="fas fa-tint"></i> Consommation d'eau
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="douche_freq">🚿 Nombre de douches par semaine *</label>
                                <input type="text" name="douche_freq" id="douche_freq" placeholder="Ex: 7">
                                <span class="error-message" id="error-douche_freq"></span>
                                <span class="success-message" id="success-douche_freq"></span>
                                <small class="form-help">Entre 1 et 50 douches par semaine</small>
                            </div>
                            
                            <div class="form-group">
                                <label for="douche_duree">⏱️ Durée moyenne d'une douche (minutes)</label>
                                <input type="text" name="douche_duree" id="douche_duree" placeholder="Ex: 10" value="10">
                                <span class="error-message" id="error-douche_duree"></span>
                                <span class="success-message" id="success-douche_duree"></span>
                                <small class="form-help">Entre 1 et 60 minutes</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section Chauffage -->
                    <div class="form-section">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #FFD54F;">
                            <i class="fas fa-fire"></i> Chauffage et énergie
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="chauffage">🔥 Type de chauffage principal *</label>
                                <select name="chauffage" id="chauffage">
                                    <option value="">-- Sélectionner un type --</option>
                                    <option value="electrique">⚡ Électrique</option>
                                    <option value="gaz">🔥 Gaz</option>
                                    <option value="fioul">🛢️ Fioul</option>
                                    <option value="bois">🪵 Bois</option>
                                    <option value="autre">🔧 Autre</option>
                                </select>
                                <span class="error-message" id="error-chauffage"></span>
                                <span class="success-message" id="success-chauffage"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="temp_hiver">🌡️ Température de consigne en hiver (°C)</label>
                                <input type="text" name="temp_hiver" id="temp_hiver" placeholder="Ex: 20" value="20">
                                <span class="error-message" id="error-temp_hiver"></span>
                                <span class="success-message" id="success-temp_hiver"></span>
                                <small class="form-help">Entre 10°C et 30°C</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section Transport -->
                    <div class="form-section">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #81C784;">
                            <i class="fas fa-car"></i> Transport et mobilité
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="transport_travail">🚗 Moyen de transport principal pour le travail *</label>
                                <select name="transport_travail" id="transport_travail">
                                    <option value="">-- Sélectionner un moyen --</option>
                                    <option value="voiture">🚗 Voiture personnelle</option>
                                    <option value="transport_commun">🚌 Transport en commun</option>
                                    <option value="velo">🚴 Vélo</option>
                                    <option value="marche">🚶 Marche à pied</option>
                                    <option value="teletravail">💻 Télétravail</option>
                                </select>
                                <span class="error-message" id="error-transport_travail"></span>
                                <span class="success-message" id="success-transport_travail"></span>
                            </div>
                            
                            <div class="form-group">
                                <label for="distance_travail">📏 Distance domicile-travail (km)</label>
                                <input type="text" name="distance_travail" id="distance_travail" placeholder="Ex: 15" value="0">
                                <span class="error-message" id="error-distance_travail"></span>
                                <span class="success-message" id="success-distance_travail"></span>
                                <small class="form-help">Entre 0 et 200 km</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="form-actions">
                        <button type="button" id="reset-form-btn" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Réinitialiser
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-btn">
                            <i class="fas fa-plus-circle"></i> Ajouter la réponse
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <style>
        .form-section {
            margin-bottom: 30px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .form-group {
            position: relative;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #013220;
            margin-bottom: 8px;
            font-size: 14px;
        }
        
        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: white;
        }
        
        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #A8E6CF;
            box-shadow: 0 0 0 3px rgba(168, 230, 207, 0.2);
        }
        
        .form-group input.error,
        .form-group select.error {
            border-color: #f44336;
            background: #ffebee;
        }
        
        .form-group input.success,
        .form-group select.success {
            border-color: #4caf50;
            background: #e8f5e9;
        }
        
        .error-message {
            color: #f44336;
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }
        
        .success-message {
            color: #4caf50;
            font-size: 12px;
            margin-top: 5px;
            display: block;
            font-weight: 500;
        }
        
        .form-help {
            color: #666;
            font-size: 11px;
            margin-top: 3px;
            display: block;
        }
        
        .form-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .btn-secondary {
            background: #f5f5f5;
            color: #666;
            border: 2px solid #ddd;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-secondary:hover {
            background: #e0e0e0;
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #A8E6CF, #013220);
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(168, 230, 207, 0.4);
        }
        
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

        <!-- FILTRES -->
        <div class="section" style="padding: 20px; margin-bottom: 20px;">
            <div style="display: flex; gap: 15px; align-items: center; flex-wrap: wrap;">
                <label style="font-weight: 600; color: #013220;">Filtrer par :</label>
                <button class="filter-btn-reponse active" data-filter-reponse="all">
                    <i class="fas fa-globe"></i> Toutes (<?= count($reponses) ?>)
                </button>
                <button class="filter-btn-reponse" data-filter-reponse="today">
                    <i class="fas fa-calendar-day"></i> Aujourd'hui (<?= $aujourdhui ?>)
                </button>
                <input type="text" id="search-reponse" placeholder="🔍 Rechercher par email..." style="padding: 10px 20px; border: 2px solid #e0e0e0; border-radius: 50px; font-size: 14px; min-width: 250px;">
            </div>
        </div>

        <!-- LISTE DES RÉPONSES -->
        <?php if ($reponses): ?>
        <div class="section">
            <h2><i class="fas fa-list"></i> Toutes les réponses (<?= count($reponses) ?>)</h2>
            <div class="reponses-grid">
                <?php foreach ($reponses as $r): ?>
                <div class="reponse-card" data-date="<?= date('Y-m-d', strtotime($r->date_soumission)) ?>" data-email="<?= strtolower(htmlspecialchars($r->email ?? '')) ?>">
                    <div class="reponse-header">
                        <span class="reponse-id">#<?= $r->idformulaire ?></span>
                        <span class="reponse-date">
                            <i class="fas fa-calendar"></i> <?= date('d/m/Y H:i', strtotime($r->date_soumission)) ?>
                        </span>
                    </div>
                    
                    <div class="reponse-body">
                        <div class="reponse-info">
                            <i class="fas fa-envelope"></i>
                            <span><?= htmlspecialchars($r->email ?? 'Non renseigné') ?></span>
                        </div>
                        
                        <div class="reponse-stats">
                            <div class="reponse-stat-item">
                                <i class="fas fa-users"></i>
                                <span><?= $r->nb_personnes ?> pers.</span>
                            </div>
                            <div class="reponse-stat-item">
                                <i class="fas fa-shower"></i>
                                <span><?= $r->douche_freq ?>x/sem (<?= $r->douche_duree ?? 10 ?>min)</span>
                            </div>
                            <div class="reponse-stat-item">
                                <i class="fas fa-fire"></i>
                                <span><?= ucfirst($r->chauffage ?? '—') ?> (<?= $r->temp_hiver ?? 20 ?>°C)</span>
                            </div>
                            <div class="reponse-stat-item">
                                <i class="fas fa-car"></i>
                                <span><?= htmlspecialchars($r->transport_travail ?? '—') ?> (<?= $r->distance_travail ?? 0 ?>km)</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="reponse-actions">
                        <button class="btn btn-info" onclick="openEditReponseModal(<?= $r->idformulaire ?>, '<?= htmlspecialchars($r->email ?? '', ENT_QUOTES) ?>', <?= $r->nb_personnes ?>, <?= $r->douche_freq ?>, <?= $r->douche_duree ?? 10 ?>, '<?= htmlspecialchars($r->chauffage ?? '', ENT_QUOTES) ?>', <?= $r->temp_hiver ?? 20 ?>, '<?= htmlspecialchars($r->transport_travail ?? '', ENT_QUOTES) ?>', <?= $r->distance_travail ?? 0 ?>)">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <a href="voir_conseil.php?id=<?= $r->idformulaire ?>" class="btn btn-info">
                            <i class="fas fa-eye"></i> Voir conseils
                        </a>
                        <a href="?delete=<?= $r->idformulaire ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette réponse ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
            <div class="section">
                <div style="text-align: center; padding: 60px; color: #666; background: #f5f5f5; border-radius: 15px;">
                    <i class="fas fa-inbox" style="font-size: 48px; display: block; margin-bottom: 20px; opacity: 0.3;"></i>
                    <p style="font-size: 18px; margin: 0;">Aucune réponse pour le moment</p>
                    <p style="margin: 10px 0 0 0; color: #999;">Ajoutez une réponse ou attendez que les utilisateurs remplissent le formulaire</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de modification réponse -->
<div id="editReponseModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier la réponse</h2>
            <span class="close-modal" onclick="closeEditReponseModal()">&times;</span>
        </div>
        <form method="post" id="form-edit-reponse">
            <input type="hidden" name="action" value="update_reponse">
            <input type="hidden" name="idformulaire" id="edit-reponse-id">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div class="form-group">
                    <label for="edit-reponse-email">Email</label>
                    <input type="email" name="email" id="edit-reponse-email" placeholder="Email (optionnel)">
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-personnes">Nombre de personnes *</label>
                    <input type="number" name="nb_personnes" id="edit-reponse-personnes" min="1" max="20" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-douches">Douches par semaine *</label>
                    <input type="number" name="douche_freq" id="edit-reponse-douches" min="1" max="50" required>
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-duree">Durée douche (min)</label>
                    <input type="number" name="douche_duree" id="edit-reponse-duree" min="1" max="60">
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-chauffage">Type de chauffage *</label>
                    <select name="chauffage" id="edit-reponse-chauffage" required>
                        <option value="">Choisir un type</option>
                        <option value="electrique">Électrique</option>
                        <option value="gaz">Gaz</option>
                        <option value="fioul">Fioul</option>
                        <option value="bois">Bois</option>
                        <option value="autre">Autre</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-temp">Température hiver (°C)</label>
                    <input type="number" name="temp_hiver" id="edit-reponse-temp" min="10" max="30">
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-transport">Moyen de transport *</label>
                    <select name="transport_travail" id="edit-reponse-transport" required>
                        <option value="">Choisir un moyen</option>
                        <option value="voiture">Voiture</option>
                        <option value="transport_commun">Transport en commun</option>
                        <option value="velo">Vélo</option>
                        <option value="marche">Marche</option>
                        <option value="teletravail">Télétravail</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit-reponse-distance">Distance travail (km)</label>
                    <input type="number" name="distance_travail" id="edit-reponse-distance" min="0" max="200">
                </div>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeEditReponseModal()">Annuler</button>
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
// ==================== GESTION DU FORMULAIRE D'AJOUT ====================

document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.getElementById('toggle-form-btn');
    const formContainer = document.getElementById('add-form-container');
    const form = document.getElementById('form-add-reponse');
    const resetBtn = document.getElementById('reset-form-btn');
    
    // Toggle du formulaire
    toggleBtn.addEventListener('click', function() {
        if (formContainer.style.display === 'none') {
            formContainer.style.display = 'block';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Masquer le formulaire';
            toggleBtn.style.background = '#f44336';
            toggleBtn.style.color = 'white';
        } else {
            formContainer.style.display = 'none';
            toggleBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Afficher le formulaire';
            toggleBtn.style.background = '#A8E6CF';
            toggleBtn.style.color = '#013220';
        }
    });
    
    // Réinitialiser le formulaire
    resetBtn.addEventListener('click', function() {
        form.reset();
        clearAllValidation();
        document.getElementById('douche_duree').value = '10';
        document.getElementById('temp_hiver').value = '20';
        document.getElementById('distance_travail').value = '0';
    });
    
    // Validation en temps réel
    setupRealTimeValidation();
    
    // Validation à la soumission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (validateForm()) {
            this.submit();
        }
    });
});

// ==================== FONCTIONS DE VALIDATION ====================

function setupRealTimeValidation() {
    // Email
    document.getElementById('email').addEventListener('blur', function() {
        validateEmail(this.value, 'email');
    });
    
    // Nombre de personnes
    document.getElementById('nb_personnes').addEventListener('input', function() {
        validateNumber(this.value, 'nb_personnes', 1, 20, 'Le nombre de personnes');
    });
    
    // Douches par semaine
    document.getElementById('douche_freq').addEventListener('input', function() {
        validateNumber(this.value, 'douche_freq', 1, 50, 'Le nombre de douches');
    });
    
    // Durée douche
    document.getElementById('douche_duree').addEventListener('input', function() {
        validateNumber(this.value, 'douche_duree', 1, 60, 'La durée de douche');
    });
    
    // Chauffage
    document.getElementById('chauffage').addEventListener('change', function() {
        validateRequired(this.value, 'chauffage', 'Le type de chauffage');
    });
    
    // Température
    document.getElementById('temp_hiver').addEventListener('input', function() {
        validateNumber(this.value, 'temp_hiver', 10, 30, 'La température');
    });
    
    // Transport
    document.getElementById('transport_travail').addEventListener('change', function() {
        validateRequired(this.value, 'transport_travail', 'Le moyen de transport');
    });
    
    // Distance
    document.getElementById('distance_travail').addEventListener('input', function() {
        validateNumber(this.value, 'distance_travail', 0, 200, 'La distance');
    });
}

function validateEmail(value, fieldId) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById('error-' + fieldId);
    const successSpan = document.getElementById('success-' + fieldId);
    
    // Email optionnel
    if (!value.trim()) {
        setFieldNeutral(field, errorSpan, successSpan);
        return true;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(value)) {
        setFieldError(field, errorSpan, successSpan, '❌ Format d\'email invalide');
        return false;
    }
    
    setFieldSuccess(field, errorSpan, successSpan, '✅ Email valide');
    return true;
}

function validateNumber(value, fieldId, min, max, fieldName) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById('error-' + fieldId);
    const successSpan = document.getElementById('success-' + fieldId);
    
    if (!value.trim()) {
        if (fieldId === 'douche_duree' || fieldId === 'temp_hiver' || fieldId === 'distance_travail') {
            setFieldNeutral(field, errorSpan, successSpan);
            return true; // Champs optionnels
        }
        setFieldError(field, errorSpan, successSpan, `❌ ${fieldName} est obligatoire`);
        return false;
    }
    
    const num = parseFloat(value);
    if (isNaN(num)) {
        setFieldError(field, errorSpan, successSpan, `❌ ${fieldName} doit être un nombre`);
        return false;
    }
    
    if (num < min || num > max) {
        setFieldError(field, errorSpan, successSpan, `❌ ${fieldName} doit être entre ${min} et ${max}`);
        return false;
    }
    
    setFieldSuccess(field, errorSpan, successSpan, `✅ ${fieldName} valide`);
    return true;
}

function validateRequired(value, fieldId, fieldName) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById('error-' + fieldId);
    const successSpan = document.getElementById('success-' + fieldId);
    
    if (!value.trim()) {
        setFieldError(field, errorSpan, successSpan, `❌ ${fieldName} est obligatoire`);
        return false;
    }
    
    setFieldSuccess(field, errorSpan, successSpan, `✅ ${fieldName} sélectionné`);
    return true;
}

function setFieldError(field, errorSpan, successSpan, message) {
    field.classList.remove('success');
    field.classList.add('error');
    errorSpan.textContent = message;
    successSpan.textContent = '';
}

function setFieldSuccess(field, errorSpan, successSpan, message) {
    field.classList.remove('error');
    field.classList.add('success');
    errorSpan.textContent = '';
    successSpan.textContent = message;
}

function setFieldNeutral(field, errorSpan, successSpan) {
    field.classList.remove('error', 'success');
    errorSpan.textContent = '';
    successSpan.textContent = '';
}

function clearAllValidation() {
    const fields = ['email', 'nb_personnes', 'douche_freq', 'douche_duree', 'chauffage', 'temp_hiver', 'transport_travail', 'distance_travail'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const errorSpan = document.getElementById('error-' + fieldId);
        const successSpan = document.getElementById('success-' + fieldId);
        setFieldNeutral(field, errorSpan, successSpan);
    });
}

function validateForm() {
    let isValid = true;
    
    // Validation complète
    if (!validateEmail(document.getElementById('email').value, 'email')) isValid = false;
    if (!validateNumber(document.getElementById('nb_personnes').value, 'nb_personnes', 1, 20, 'Le nombre de personnes')) isValid = false;
    if (!validateNumber(document.getElementById('douche_freq').value, 'douche_freq', 1, 50, 'Le nombre de douches')) isValid = false;
    if (!validateNumber(document.getElementById('douche_duree').value, 'douche_duree', 1, 60, 'La durée de douche')) isValid = false;
    if (!validateRequired(document.getElementById('chauffage').value, 'chauffage', 'Le type de chauffage')) isValid = false;
    if (!validateNumber(document.getElementById('temp_hiver').value, 'temp_hiver', 10, 30, 'La température')) isValid = false;
    if (!validateRequired(document.getElementById('transport_travail').value, 'transport_travail', 'Le moyen de transport')) isValid = false;
    if (!validateNumber(document.getElementById('distance_travail').value, 'distance_travail', 0, 200, 'La distance')) isValid = false;
    
    if (!isValid) {
        showNotification('❌ Veuillez corriger les erreurs dans le formulaire', 'error');
        // Scroll vers la première erreur
        const firstError = document.querySelector('.form-group input.error, .form-group select.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    } else {
        showNotification('✅ Formulaire valide, ajout en cours...', 'success');
    }
    
    return isValid;
}

// ==================== MODAL DE MODIFICATION ====================

function openEditReponseModal(id, email, personnes, douches, duree, chauffage, temp, transport, distance) {
    document.getElementById('edit-reponse-id').value = id;
    document.getElementById('edit-reponse-email').value = email;
    document.getElementById('edit-reponse-personnes').value = personnes;
    document.getElementById('edit-reponse-douches').value = douches;
    document.getElementById('edit-reponse-duree').value = duree;
    document.getElementById('edit-reponse-chauffage').value = chauffage;
    document.getElementById('edit-reponse-temp').value = temp;
    document.getElementById('edit-reponse-transport').value = transport;
    document.getElementById('edit-reponse-distance').value = distance;
    document.getElementById('editReponseModal').classList.add('active');
}

function closeEditReponseModal() {
    document.getElementById('editReponseModal').classList.remove('active');
}

// Fermer le modal en cliquant en dehors
document.getElementById('editReponseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeEditReponseModal();
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditReponseModal();
    }
});

// ==================== NOTIFICATIONS ====================

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'js-notification' + (type === 'error' ? ' error' : '');
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    notification.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Supprimer après 5 secondes
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Vérifier s'il y a une notification à afficher
<?php if (isset($_SESSION['notification'])): ?>
    showNotification('<?= addslashes($_SESSION['notification']) ?>', '<?= $_SESSION['notification_type'] ?? 'success' ?>');
    <?php 
    unset($_SESSION['notification']); 
    unset($_SESSION['notification_type']);
    ?>
<?php endif; ?>

<?= getNotificationJavaScript() ?>

// ==================== FILTRES ET RECHERCHE ====================

const filterBtnsReponse = document.querySelectorAll('.filter-btn-reponse');
const reponseCards = document.querySelectorAll('.reponse-card');
const searchReponse = document.getElementById('search-reponse');
const today = new Date().toISOString().split('T')[0];

// Filtres par bouton
filterBtnsReponse.forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter-reponse');
        
        // Retirer active de tous les filtres
        filterBtnsReponse.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Réinitialiser la recherche
        if (searchReponse) searchReponse.value = '';
        
        // Filtrer les cartes
        reponseCards.forEach(card => {
            const cardDate = card.getAttribute('data-date');
            
            if (filter === 'all') {
                card.style.display = 'flex';
            } else if (filter === 'today' && cardDate === today) {
                card.style.display = 'flex';
            } else if (filter === 'today' && cardDate !== today) {
                card.style.display = 'none';
            }
            
            if (card.style.display === 'flex') {
                card.style.animation = 'fadeIn 0.4s ease';
            }
        });
    });
});

// Recherche par email
if (searchReponse) {
    searchReponse.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        // Réinitialiser les filtres de bouton
        filterBtnsReponse.forEach(b => b.classList.remove('active'));
        
        reponseCards.forEach(card => {
            const email = card.getAttribute('data-email');
            
            if (email.includes(searchTerm)) {
                card.style.display = 'flex';
                card.style.animation = 'fadeIn 0.4s ease';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Si la recherche est vide, réactiver le filtre "Toutes"
        if (searchTerm === '') {
            filterBtnsReponse[0].classList.add('active');
        }
    });
}
</script>
</body>
</html>