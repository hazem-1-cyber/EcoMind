<?php
session_start();
require_once '../../config.php';
require_once 'notifications.php';

$PASSWORD = 'ecomind'; 

// === CONNEXION ADMIN ===
if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    if (!isset($_POST['password']) || $_POST['password'] !== $PASSWORD) {
        die('<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>EcoMind - Admin</title>
        <style>
            body{background:linear-gradient(135deg,#013220,#001a10);color:#A8E6CF;font-family:"Inter",sans-serif;display:flex;justify-content:center;align-items:center;height:100vh;margin:0;}
            .login{background:rgba(255,255,255,0.1);backdrop-filter:blur(20px);padding:70px;border-radius:30px;box-shadow:0 20px 60px rgba(0,0,0,0.6);text-align:center;width:420px;}
            input,button{width:100%;padding:18px;margin:15px 0;border:none;border-radius:50px;font-size:18px;}
            input{background:rgba(255,255,255,0.95);text-align:center;color:#013220;}
            button{background:#A8E6CF;color:#013220;font-weight:bold;cursor:pointer;transition:.3s;}
            button:hover{background:white;transform:scale(1.05);}
            h1{font-size:42px;margin-bottom:30px;}
        </style></head><body>
        <div class="login">
            <h1>EcoMind Admin</h1>
            <form method="post">
                <input type="password" name="password" placeholder="Mot de passe" required autofocus>
                <button type="submit">Connexion</button>
            </form>
        </div></body></html>');
    }
    $_SESSION['admin_logged'] = true;
}

// Déconnexion
if (isset($_GET['deconnexion'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

$db = Config::getConnexion();

// === STATS ===
$totalReponses   = $db->query("SELECT COUNT(*) FROM reponse_formulaire")->fetchColumn();
$aujourdhui      = $db->query("SELECT COUNT(*) FROM reponse_formulaire WHERE DATE(date_soumission) = CURDATE()")->fetchColumn();
$conseilsDonnes  = $totalReponses * 3;
$totalConseils   = $db->query("SELECT COUNT(*) FROM conseil")->fetchColumn();
$totalPersonnes  = $db->query("SELECT SUM(nb_personnes) FROM reponse_formulaire")->fetchColumn() ?: 0;

// === STATS POUR GRAPHIQUES ===
// Réponses par jour (7 derniers jours)
$reponsesParJour = $db->query("
    SELECT DATE(date_soumission) as date, COUNT(*) as count 
    FROM reponse_formulaire 
    WHERE date_soumission >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(date_soumission)
    ORDER BY date
")->fetchAll(PDO::FETCH_ASSOC);

// Conseils par catégorie
$conseilsParType = $db->query("
    SELECT type, COUNT(*) as count 
    FROM conseil 
    GROUP BY type
")->fetchAll(PDO::FETCH_ASSOC);

// Types de chauffage
$chauffageStats = $db->query("
    SELECT chauffage, COUNT(*) as count 
    FROM reponse_formulaire 
    WHERE chauffage IS NOT NULL
    GROUP BY chauffage
")->fetchAll(PDO::FETCH_ASSOC);

// Moyens de transport
$transportStats = $db->query("
    SELECT transport_travail, COUNT(*) as count 
    FROM reponse_formulaire 
    WHERE transport_travail IS NOT NULL
    GROUP BY transport_travail
")->fetchAll(PDO::FETCH_ASSOC);

// === AJOUT CONSEIL ===
$message = '';
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $type = trim($_POST['type'] ?? '');
    $texte = trim($_POST['texte'] ?? '');
    if ($type && $texte) {
        $stmt = $db->prepare("INSERT INTO conseil (type, texte) VALUES (?, ?)");
        $stmt->execute([$type, $texte]);
        $_SESSION['notification'] = 'Conseil ajouté avec succès !';
        $_SESSION['active_section'] = 'conseils';
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Tous les champs sont obligatoires.';
        $_SESSION['notification_type'] = 'error';
        $_SESSION['active_section'] = 'conseils';
        header("Location: index.php");
        exit;
    }
}

// === MODIFICATION CONSEIL ===
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['idconseil'];
    $type = trim($_POST['type'] ?? '');
    $texte = trim($_POST['texte'] ?? '');
    if ($id && $type && $texte && in_array($type, ['eau','energie','transport'])) {
        $stmt = $db->prepare("UPDATE conseil SET type = ?, texte = ? WHERE idconseil = ?");
        $stmt->execute([$type, $texte, $id]);
        $_SESSION['notification'] = 'Conseil modifié avec succès !';
        $_SESSION['active_section'] = 'conseils';
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Erreur lors de la modification.';
        $_SESSION['notification_type'] = 'error';
        $_SESSION['active_section'] = 'conseils';
        header("Location: index.php");
        exit;
    }
}

// === SUPPRESSION CONSEIL ===
if (isset($_GET['delete_conseil'])) {
    $id = (int)$_GET['delete_conseil'];
    $db->prepare("DELETE FROM conseil WHERE idconseil = ?")->execute([$id]);
    $_SESSION['notification'] = 'Conseil supprimé avec succès !';
    $_SESSION['active_section'] = 'conseils';
    header("Location: index.php");
    exit;
}

// === SUPPRESSION RÉPONSE ===
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM reponse_formulaire WHERE idformulaire = ?")->execute([$id]);
    $_SESSION['notification'] = 'Réponse supprimée avec succès !';
    $_SESSION['active_section'] = 'reponses';
    header("Location: index.php");
    exit;
}

// === AJOUT RÉPONSE ===
if (isset($_POST['action']) && $_POST['action'] === 'add_reponse') {
    $email = trim($_POST['email'] ?? '');
    $nb_personnes = (int)($_POST['nb_personnes'] ?? 0);
    $douche_freq = (int)($_POST['douche_freq'] ?? 0);
    $chauffage = trim($_POST['chauffage'] ?? '');
    $transport = trim($_POST['transport_travail'] ?? '');
    
    if ($nb_personnes > 0 && $douche_freq > 0 && $chauffage && $transport) {
        $stmt = $db->prepare("INSERT INTO reponse_formulaire (email, nb_personnes, douche_freq, chauffage, transport_travail, date_soumission) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$email, $nb_personnes, $douche_freq, $chauffage, $transport]);
        $_SESSION['notification'] = 'Réponse ajoutée avec succès !';
        $_SESSION['active_section'] = 'reponses';
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Tous les champs sont obligatoires.';
        $_SESSION['notification_type'] = 'error';
        $_SESSION['active_section'] = 'reponses';
        header("Location: index.php");
        exit;
    }
}

// === MODIFICATION RÉPONSE ===
if (isset($_POST['action']) && $_POST['action'] === 'update_reponse') {
    $id = (int)$_POST['idformulaire'];
    $email = trim($_POST['email'] ?? '');
    $nb_personnes = (int)($_POST['nb_personnes'] ?? 0);
    $douche_freq = (int)($_POST['douche_freq'] ?? 0);
    $chauffage = trim($_POST['chauffage'] ?? '');
    $transport = trim($_POST['transport_travail'] ?? '');
    
    if ($id && $nb_personnes > 0 && $douche_freq > 0 && $chauffage && $transport) {
        $stmt = $db->prepare("UPDATE reponse_formulaire SET email = ?, nb_personnes = ?, douche_freq = ?, chauffage = ?, transport_travail = ? WHERE idformulaire = ?");
        $stmt->execute([$email, $nb_personnes, $douche_freq, $chauffage, $transport, $id]);
        $_SESSION['notification'] = 'Réponse modifiée avec succès !';
        $_SESSION['active_section'] = 'reponses';
        header("Location: index.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Erreur lors de la modification.';
        $_SESSION['notification_type'] = 'error';
        $_SESSION['active_section'] = 'reponses';
        header("Location: index.php");
        exit;
    }
}

// Les notifications seront affichées par JavaScript

// === LISTE DES CONSEILS ===
$conseils = $db->query("SELECT * FROM conseil ORDER BY type, idconseil DESC")->fetchAll(PDO::FETCH_OBJ);

// === 20 DERNIÈRES RÉPONSES ===
$reponses = $db->query("SELECT * FROM reponse_formulaire ORDER BY date_soumission DESC LIMIT 20")->fetchAll(PDO::FETCH_OBJ);

// Générer les notifications pour le dashboard
$notifications = genererNotificationsPage($db, 'dashboard');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - Backoffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/backoffice.css?v=<?php echo time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
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
        <a href="index.php" class="nav-item active">
            <i class="fas fa-home"></i>
            <span>Tableau de bord</span>
        </a>
        <a href="gerer_conseils.php" class="nav-item">
            <i class="fas fa-cog"></i>
            <span>Gestion de Conseils</span>
        </a>
        <a href="gestion_reponses.php" class="nav-item">
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
        <h1>EcoMind - Backoffice</h1>
        <div style="display: flex; align-items: center; gap: 15px;">
            <!-- Cloche de notifications -->
            <?= renderNotificationBell($notifications) ?>
            <a href="?deconnexion=1" class="logout-btn">Déconnexion</a>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
    <div style="background: #ffebee; border-left: 4px solid #f44336; padding: 15px; margin: 20px 0; border-radius: 5px;">
        <strong style="color: #c62828;">❌ Erreur :</strong>
        <span style="color: #333;"><?= htmlspecialchars($_SESSION['error']) ?></span>
    </div>
    <?php unset($_SESSION['error']); endif; ?>

    <!-- DASHBOARD CONTENT -->
    <div class="content-section active">
        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-poll"></i></div>
                <p>Total réponses</p>
                <h3><?= $totalReponses ?></h3>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-users"></i></div>
                <p>Total personnes</p>
                <h3><?= $totalPersonnes ?></h3>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-calendar-day"></i></div>
                <p>Aujourd'hui</p>
                <h3><?= $aujourdhui ?></h3>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <p>Conseils donnés</p>
                <h3><?= $conseilsDonnes ?></h3>
            </div>
            <div class="stat-card">
                <div class="icon"><i class="fas fa-database"></i></div>
                <p>Conseils en base</p>
                <h3><?= $totalConseils ?></h3>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05)); border-left: 4px solid #667eea;">
                <div class="icon" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;">🤖</div>
                <p>Mode IA Actif</p>
                <h3 style="font-size: 1.2em; color: #667eea;">100%</h3>
                <a href="gestion_ia.php" style="display: inline-block; margin-top: 10px; padding: 8px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 20px; font-size: 0.85em; transition: all 0.3s;">
                    Gérer l'IA →
                </a>
            </div>
        </div>

        <div class="section">
            <h2>Vue d'ensemble</h2>
            <p style="font-size:16px;color:#666;line-height:1.8;">
                Bienvenue sur le backoffice EcoMind ! Vous avez actuellement <strong><?= $totalReponses ?></strong> réponses enregistrées 
                et <strong><?= $totalConseils ?></strong> conseils dans la base de données. 
                Utilisez le menu de gauche pour naviguer entre les différentes sections.
            </p>
        </div>

        <!-- GRAPHIQUES -->
        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(500px,1fr));gap:25px;margin-top:30px;">
            <!-- Graphique réponses par jour -->
            <div class="section">
                <h2><i class="fas fa-chart-line"></i> Réponses des 7 derniers jours</h2>
                <canvas id="chartReponses" style="max-height:300px;"></canvas>
            </div>

            <!-- Graphique conseils par catégorie -->
            <div class="section">
                <h2><i class="fas fa-chart-pie"></i> Conseils par catégorie</h2>
                <canvas id="chartConseils" style="max-height:300px;"></canvas>
            </div>

            <!-- Graphique types de chauffage -->
            <div class="section">
                <h2><i class="fas fa-fire"></i> Types de chauffage</h2>
                <canvas id="chartChauffage" style="max-height:300px;"></canvas>
            </div>

            <!-- Graphique moyens de transport -->
            <div class="section">
                <h2><i class="fas fa-car"></i> Moyens de transport</h2>
                <canvas id="chartTransport" style="max-height:300px;"></canvas>
            </div>
        </div>
    </div>

</div><!-- fin dashboard-container -->

<!-- Modal de modification conseil -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier le conseil</h2>
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="post" id="form-edit-conseil">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="idconseil" id="edit-id">
            
            <div class="form-group">
                <label for="edit-type">Catégorie</label>
                <select name="type" id="edit-type">
                    <option value="eau">💧 Eau</option>
                    <option value="energie">⚡ Énergie</option>
                    <option value="transport">🚗 Transport</option>
                </select>
                <span class="error-message" id="error-edit-type"></span>
            </div>
            
            <div class="form-group">
                <label for="edit-texte">Texte du conseil</label>
                <textarea name="texte" id="edit-texte" placeholder="Entrez le conseil écologique..."></textarea>
                <span class="error-message" id="error-edit-texte"></span>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
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
            
            <div class="form-group">
                <label for="edit-reponse-email">Email</label>
                <input type="email" name="email" id="edit-reponse-email" placeholder="Email (optionnel)" style="width:100%;padding:12px;border:2px solid #e0e0e0;border-radius:10px;">
                <span class="error-message" id="error-edit-reponse-email"></span>
            </div>
            
            <div class="form-group">
                <label for="edit-reponse-personnes">Nombre de personnes</label>
                <input type="number" name="nb_personnes" id="edit-reponse-personnes" min="1" style="width:100%;padding:12px;border:2px solid #e0e0e0;border-radius:10px;">
                <span class="error-message" id="error-edit-reponse-personnes"></span>
            </div>
            
            <div class="form-group">
                <label for="edit-reponse-douches">Douches par semaine</label>
                <input type="number" name="douche_freq" id="edit-reponse-douches" min="1" style="width:100%;padding:12px;border:2px solid #e0e0e0;border-radius:10px;">
                <span class="error-message" id="error-edit-reponse-douches"></span>
            </div>
            
            <div class="form-group">
                <label for="edit-reponse-chauffage">Type de chauffage</label>
                <select name="chauffage" id="edit-reponse-chauffage">
                    <option value="">Choisir un type</option>
                    <option value="electrique">Électrique</option>
                    <option value="gaz">Gaz</option>
                    <option value="pompe_a_chaleur">Pompe à chaleur</option>
                    <option value="bois">Bois</option>
                    <option value="autre">Autre</option>
                </select>
                <span class="error-message" id="error-edit-reponse-chauffage"></span>
            </div>
            
            <div class="form-group">
                <label for="edit-reponse-transport">Moyen de transport</label>
                <select name="transport_travail" id="edit-reponse-transport">
                    <option value="">Choisir un moyen</option>
                    <option value="voiture">Voiture</option>
                    <option value="transport_commun">Transport en commun</option>
                    <option value="velo">Vélo</option>
                    <option value="marche">Marche</option>
                    <option value="teletravail">Télétravail</option>
                </select>
                <span class="error-message" id="error-edit-reponse-transport"></span>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeEditReponseModal()">Annuler</button>
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
// Navigation sidebar - simplified for direct page navigation
document.addEventListener('DOMContentLoaded', function() {
    // All navigation is now handled by direct page links
    // No need for complex section switching
});

// Modal de modification conseil
function openEditModal(id, type, texte) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-type').value = type;
    document.getElementById('edit-texte').value = texte;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Modal de modification réponse
function openEditReponseModal(id, email, personnes, douches, chauffage, transport) {
    document.getElementById('edit-reponse-id').value = id;
    document.getElementById('edit-reponse-email').value = email;
    document.getElementById('edit-reponse-personnes').value = personnes;
    document.getElementById('edit-reponse-douches').value = douches;
    document.getElementById('edit-reponse-chauffage').value = chauffage;
    document.getElementById('edit-reponse-transport').value = transport;
    document.getElementById('editReponseModal').classList.add('active');
}

function closeEditReponseModal() {
    document.getElementById('editReponseModal').classList.remove('active');
}

// Fermer les modals en cliquant en dehors
document.addEventListener('click', function(e) {
    const modalConseil = document.getElementById('editModal');
    const modalReponse = document.getElementById('editReponseModal');
    
    if (e.target === modalConseil) {
        closeEditModal();
    }
    if (e.target === modalReponse) {
        closeEditReponseModal();
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
        closeEditReponseModal();
    }
});

// Fonction pour afficher une notification JavaScript
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

// ==================== VALIDATION MODALS ====================

// Validation modal modification conseil
document.getElementById('form-edit-conseil').addEventListener('submit', function(e) {
    e.preventDefault();
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('#editModal .error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('#editModal input, #editModal select, #editModal textarea').forEach(el => el.classList.remove('error', 'success'));
    
    const type = document.getElementById('edit-type');
    const texte = document.getElementById('edit-texte');
    
    // Validation type
    if (!type.value) {
        document.getElementById('error-edit-type').textContent = '⚠️ Veuillez choisir une catégorie';
        type.classList.add('error');
        isValid = false;
    } else {
        type.classList.add('success');
    }
    
    // Validation texte
    if (!texte.value.trim()) {
        document.getElementById('error-edit-texte').textContent = '⚠️ Le texte du conseil est obligatoire';
        texte.classList.add('error');
        isValid = false;
    } else if (texte.value.trim().length < 10) {
        document.getElementById('error-edit-texte').textContent = '⚠️ Le conseil doit contenir au moins 10 caractères';
        texte.classList.add('error');
        isValid = false;
    } else {
        texte.classList.add('success');
    }
    
    if (isValid) {
        this.submit();
    }
});

// Validation modal modification réponse
document.getElementById('form-edit-reponse').addEventListener('submit', function(e) {
    e.preventDefault();
    let isValid = true;
    
    // Reset errors
    document.querySelectorAll('#editReponseModal .error-message').forEach(el => el.textContent = '');
    document.querySelectorAll('#editReponseModal input, #editReponseModal select').forEach(el => el.classList.remove('error', 'success'));
    
    const email = document.getElementById('edit-reponse-email');
    const personnes = document.getElementById('edit-reponse-personnes');
    const douches = document.getElementById('edit-reponse-douches');
    const chauffage = document.getElementById('edit-reponse-chauffage');
    const transport = document.getElementById('edit-reponse-transport');
    
    // Validation email (optionnel mais doit être valide si rempli)
    if (email.value && !email.value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
        document.getElementById('error-edit-reponse-email').textContent = '⚠️ Email invalide';
        email.classList.add('error');
        isValid = false;
    } else if (email.value) {
        email.classList.add('success');
    }
    
    // Validation nombre de personnes
    if (!personnes.value || personnes.value < 1) {
        document.getElementById('error-edit-reponse-personnes').textContent = '⚠️ Minimum 1 personne';
        personnes.classList.add('error');
        isValid = false;
    } else if (personnes.value > 20) {
        document.getElementById('error-edit-reponse-personnes').textContent = '⚠️ Maximum 20 personnes';
        personnes.classList.add('error');
        isValid = false;
    } else {
        personnes.classList.add('success');
    }
    
    // Validation douches
    if (!douches.value || douches.value < 1) {
        document.getElementById('error-edit-reponse-douches').textContent = '⚠️ Minimum 1 douche';
        douches.classList.add('error');
        isValid = false;
    } else if (douches.value > 50) {
        document.getElementById('error-edit-reponse-douches').textContent = '⚠️ Maximum 50 douches';
        douches.classList.add('error');
        isValid = false;
    } else {
        douches.classList.add('success');
    }
    
    // Validation chauffage
    if (!chauffage.value) {
        document.getElementById('error-edit-reponse-chauffage').textContent = '⚠️ Choisir un type de chauffage';
        chauffage.classList.add('error');
        isValid = false;
    } else {
        chauffage.classList.add('success');
    }
    
    // Validation transport
    if (!transport.value) {
        document.getElementById('error-edit-reponse-transport').textContent = '⚠️ Choisir un moyen de transport';
        transport.classList.add('error');
        isValid = false;
    } else {
        transport.classList.add('success');
    }
    
    if (isValid) {
        this.submit();
    }
});

// Validation en temps réel dans les modals
document.getElementById('edit-texte').addEventListener('input', function() {
    const error = document.getElementById('error-edit-texte');
    if (this.value.trim().length > 0 && this.value.trim().length < 10) {
        error.textContent = `⚠️ ${this.value.trim().length}/10 caractères minimum`;
        this.classList.add('error');
        this.classList.remove('success');
    } else if (this.value.trim().length >= 10) {
        error.textContent = '';
        this.classList.remove('error');
        this.classList.add('success');
    }
});

document.getElementById('edit-reponse-personnes').addEventListener('input', function() {
    const error = document.getElementById('error-edit-reponse-personnes');
    if (this.value && (this.value < 1 || this.value > 20)) {
        error.textContent = this.value < 1 ? '⚠️ Minimum 1' : '⚠️ Maximum 20';
        this.classList.add('error');
        this.classList.remove('success');
    } else if (this.value) {
        error.textContent = '';
        this.classList.remove('error');
        this.classList.add('success');
    }
});

document.getElementById('edit-reponse-douches').addEventListener('input', function() {
    const error = document.getElementById('error-edit-reponse-douches');
    if (this.value && (this.value < 1 || this.value > 50)) {
        error.textContent = this.value < 1 ? '⚠️ Minimum 1' : '⚠️ Maximum 50';
        this.classList.add('error');
        this.classList.remove('success');
    } else if (this.value) {
        error.textContent = '';
        this.classList.remove('error');
        this.classList.add('success');
    }
});

// ==================== GRAPHIQUES CHART.JS ====================

// Configuration globale des graphiques
Chart.defaults.font.family = "'Segoe UI', Tahoma, Geneva, Verdana, sans-serif";
Chart.defaults.color = '#013220';

// Graphique réponses par jour (ligne)
const ctxReponses = document.getElementById('chartReponses');
if (ctxReponses) {
    <?php
    // Préparer les données pour les 7 derniers jours
    $labels = [];
    $data = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('d/m', strtotime($date));
        $count = 0;
        foreach ($reponsesParJour as $r) {
            if ($r['date'] === $date) {
                $count = $r['count'];
                break;
            }
        }
        $data[] = $count;
    }
    ?>
    new Chart(ctxReponses, {
        type: 'line',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                label: 'Réponses',
                data: <?= json_encode($data) ?>,
                borderColor: '#A8E6CF',
                backgroundColor: 'rgba(168, 230, 207, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#013220',
                pointBorderColor: '#A8E6CF',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    padding: 12,
                    titleColor: '#A8E6CF',
                    bodyColor: '#fff'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(168, 230, 207, 0.2)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Graphique conseils par catégorie (doughnut)
const ctxConseils = document.getElementById('chartConseils');
if (ctxConseils) {
    <?php
    $conseilLabels = [];
    $conseilData = [];
    $icons = ['eau' => '💧', 'energie' => '⚡', 'transport' => '🚗'];
    foreach ($conseilsParType as $c) {
        $conseilLabels[] = ($icons[$c['type']] ?? '') . ' ' . ucfirst($c['type']);
        $conseilData[] = $c['count'];
    }
    ?>
    new Chart(ctxConseils, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($conseilLabels) ?>,
            datasets: [{
                data: <?= json_encode($conseilData) ?>,
                backgroundColor: ['#4FC3F7', '#FFD54F', '#81C784'],
                borderColor: '#fff',
                borderWidth: 3
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, font: { size: 13 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    padding: 12
                }
            }
        }
    });
}

// Graphique types de chauffage (bar)
const ctxChauffage = document.getElementById('chartChauffage');
if (ctxChauffage) {
    <?php
    $chauffageLabels = [];
    $chauffageData = [];
    foreach ($chauffageStats as $c) {
        $chauffageLabels[] = ucfirst($c['chauffage']);
        $chauffageData[] = $c['count'];
    }
    ?>
    new Chart(ctxChauffage, {
        type: 'bar',
        data: {
            labels: <?= json_encode($chauffageLabels) ?>,
            datasets: [{
                label: 'Nombre',
                data: <?= json_encode($chauffageData) ?>,
                backgroundColor: 'rgba(168, 230, 207, 0.8)',
                borderColor: '#013220',
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    padding: 12
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(168, 230, 207, 0.2)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
}

// Graphique moyens de transport (polar area)
const ctxTransport = document.getElementById('chartTransport');
if (ctxTransport) {
    <?php
    $transportLabels = [];
    $transportData = [];
    $transportNames = [
        'voiture' => '🚗 Voiture',
        'transport_commun' => '🚌 Transport commun',
        'velo' => '🚴 Vélo',
        'marche' => '🚶 Marche',
        'teletravail' => '💻 Télétravail'
    ];
    foreach ($transportStats as $t) {
        $transportLabels[] = $transportNames[$t['transport_travail']] ?? ucfirst($t['transport_travail']);
        $transportData[] = $t['count'];
    }
    ?>
    new Chart(ctxTransport, {
        type: 'polarArea',
        data: {
            labels: <?= json_encode($transportLabels) ?>,
            datasets: [{
                data: <?= json_encode($transportData) ?>,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.7)',
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(153, 102, 255, 0.7)',
                    'rgba(255, 159, 64, 0.7)'
                ],
                borderColor: '#fff',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, font: { size: 12 } }
                },
                tooltip: {
                    backgroundColor: 'rgba(1, 50, 32, 0.9)',
                    padding: 12
                }
            },
            scales: {
                r: {
                    ticks: { stepSize: 1 },
                    grid: { color: 'rgba(168, 230, 207, 0.2)' }
                }
            }
        }
    });
}

<?= getNotificationJavaScript() ?>

// Vérifier s'il y a une notification à afficher
<?php if (isset($_SESSION['notification'])): ?>
    showNotification('<?= addslashes($_SESSION['notification']) ?>', '<?= $_SESSION['notification_type'] ?? 'success' ?>');
    <?php 
    unset($_SESSION['notification']); 
    unset($_SESSION['notification_type']);
    ?>
<?php endif; ?>

// Dashboard is now simplified - detailed management is in dedicated pages
</script>
</body>
</html>