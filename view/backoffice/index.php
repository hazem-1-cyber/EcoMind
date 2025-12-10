<?php
session_start();
require_once '../../config.php';

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

// === AJOUT CONSEIL ===
$message = '';
if (isset($_POST['action']) && $_POST['action'] === 'add') {
    $type = trim($_POST['type'] ?? '');
    $texte = trim($_POST['texte'] ?? '');
    if ($type && $texte) {
        $stmt = $db->prepare("INSERT INTO conseil (type, texte) VALUES (?, ?)");
        $stmt->execute([$type, $texte]);
        $message = '<div class="success">Conseil ajouté avec succès !</div>';
        $totalConseils++; // Mise à jour du compteur
    } else {
        $message = '<div class="error">Tous les champs sont obligatoires.</div>';
    }
}

// === MODIFICATION CONSEIL ===
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $type = trim($_POST['type'] ?? '');
    $texte = trim($_POST['texte'] ?? '');
    if ($id && $type && $texte && in_array($type, ['eau','energie','transport'])) {
        $stmt = $db->prepare("UPDATE conseil SET type = ?, texte = ? WHERE id = ?");
        $stmt->execute([$type, $texte, $id]);
        $message = '<div class="success">Conseil modifié avec succès !</div>';
    } else {
        $message = '<div class="error">Erreur lors de la modification.</div>';
    }
}

// === SUPPRESSION CONSEIL ===
if (isset($_GET['delete_conseil'])) {
    $id = (int)$_GET['delete_conseil'];
    $db->prepare("DELETE FROM conseil WHERE id = ?")->execute([$id]);
    header("Location: index.php");
    exit;
}

// === SUPPRESSION RÉPONSE ===
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM reponse_formulaire WHERE id = ?")->execute([$id]);
    header("Location: index.php");
    exit;
}

// === LISTE DES CONSEILS ===
$conseils = $db->query("SELECT * FROM conseil ORDER BY type, id DESC")->fetchAll(PDO::FETCH_OBJ);

// === 20 DERNIÈRES RÉPONSES ===
$reponses = $db->query("SELECT * FROM reponse_formulaire ORDER BY date_soumission DESC LIMIT 20")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - Backoffice</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="backoffice.css">
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="../frontoffice/assets/images/Screenshot_2025-11-16_152042-removebg-preview.png" alt="EcoMind Logo">
    </div>
    <ul>
        <li class="nav-item active" data-section="dashboard"><i class="fas fa-home"></i><span>Dashboard</span></li>
        <li class="nav-item" data-section="conseils"><i class="fas fa-lightbulb"></i><span>Conseils</span></li>
        <li class="nav-item" data-section="reponses"><i class="fas fa-users"></i><span>Réponses</span></li>
        <li class="nav-item" onclick="window.location.href='gerer_conseils.php'"><i class="fas fa-cog"></i><span>Gérer Conseils</span></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">

    <div class="top-header">
        <h1>EcoMind - Backoffice</h1>
        <a href="?deconnexion=1" class="logout-btn">Déconnexion</a>
    </div>

    <!-- SECTION DASHBOARD -->
    <div class="content-section active" id="dashboard-section">
        <!-- STATS -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-poll"></i></div>
                <p>Total réponses</p>
                <h3><?= $totalReponses ?></h3>
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
        </div>

        <div class="section">
            <h2>Vue d'ensemble</h2>
            <p style="font-size:16px;color:#666;line-height:1.8;">
                Bienvenue sur le backoffice EcoMind ! Vous avez actuellement <strong><?= $totalReponses ?></strong> réponses enregistrées 
                et <strong><?= $totalConseils ?></strong> conseils dans la base de données. 
                Utilisez le menu de gauche pour naviguer entre les différentes sections.
            </p>
        </div>
    </div>

    <!-- SECTION CONSEILS -->
    <div class="content-section" id="conseils-section">
        <!-- AJOUT CONSEIL -->
        <div class="section">
            <h2>Ajouter un nouveau conseil</h2>
            <?= $message ?>
            <div class="form-container">
                <form method="post">
                    <input type="hidden" name="action" value="add">
                    <select name="type" required>
                        <option value="">Choisir une catégorie</option>
                        <option value="eau">Eau</option>
                        <option value="energie">Énergie</option>
                        <option value="transport">Transport</option>
                    </select>
                    <textarea name="texte" rows="5" placeholder="Ex : Prenez des douches de 5 minutes maximum pour économiser jusqu'à 60L d'eau !" required></textarea>
                    <button type="submit">Ajouter le conseil</button>
                </form>
            </div>
        </div>

        <!-- TOUS LES CONSEILS -->
        <div class="section">
            <h2>Tous les conseils en base (<?= count($conseils) ?>)</h2>
            <?php if ($conseils): ?>
            <table>
                <tr><th>ID</th><th>Catégorie</th><th>Texte du conseil</th><th>Actions</th></tr>
                <?php foreach ($conseils as $c): ?>
                <tr>
                    <td><strong>#<?= $c->id ?></strong></td>
                    <td><span style="padding:8px 18px;background:#A8E6CF;color:#013220;border-radius:50px;font-weight:bold;"><?= ucfirst($c->type) ?></span></td>
                    <td><?= nl2br(htmlspecialchars($c->texte)) ?></td>
                    <td>
                        <button class="btn btn-info" onclick="openEditModal(<?= $c->id ?>, '<?= htmlspecialchars($c->type, ENT_QUOTES) ?>', `<?= htmlspecialchars($c->texte, ENT_QUOTES) ?>`)">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <a href="?delete_conseil=<?= $c->id ?>" class="btn btn-danger" onclick="return confirm('Supprimer définitivement ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                <p style="text-align:center;color:#666;padding:40px;font-size:18px;">Aucun conseil pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- SECTION RÉPONSES -->
    <div class="content-section" id="reponses-section">
        <div class="section">
            <h2>20 dernières réponses</h2>
            <?php if ($reponses): ?>
            <table>
                <tr><th>ID</th><th>Date</th><th>Email</th><th>Personnes</th><th>Douches</th><th>Chauffage</th><th>Transport</th><th>Actions</th></tr>
                <?php foreach ($reponses as $r): ?>
                <tr>
                    <td><strong>#<?= $r->id ?></strong></td>
                    <td><?= date('d/m/Y H:i', strtotime($r->date_soumission)) ?></td>
                    <td><?= htmlspecialchars($r->email ?? '—') ?></td>
                    <td><?= $r->nb_personnes ?></td>
                    <td><?= $r->douche_freq ?></td>
                    <td><?= ucfirst($r->chauffage ?? '—') ?></td>
                    <td><?= htmlspecialchars($r->transport_travail ?? '—') ?></td>
                    <td>
                        <a href="voir_conseil.php?id=<?= $r->id ?>" class="btn btn-info">Voir conseils</a>
                        <a href="?delete=<?= $r->id ?>" class="btn btn-danger" onclick="return confirm('Supprimer ?')">Supprimer</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php else: ?>
                <p style="text-align:center;color:#666;padding:40px;font-size:18px;">Aucune réponse pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

</div>

<!-- Modal de modification -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier le conseil</h2>
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
        </div>
        <form method="post">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="edit-id">
            
            <div class="form-group">
                <label for="edit-type">Catégorie</label>
                <select name="type" id="edit-type" required>
                    <option value="eau">💧 Eau</option>
                    <option value="energie">⚡ Énergie</option>
                    <option value="transport">🚗 Transport</option>
                </select>
            </div>
            
            <div class="form-group">
                <label for="edit-texte">Texte du conseil</label>
                <textarea name="texte" id="edit-texte" required placeholder="Entrez le conseil écologique..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="btn btn-save"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
// Navigation sidebar
document.addEventListener('DOMContentLoaded', function() {
    const navItems = document.querySelectorAll('.nav-item[data-section]');
    const sections = document.querySelectorAll('.content-section');
    
    navItems.forEach(item => {
        item.addEventListener('click', function() {
            const targetSection = this.getAttribute('data-section');
            
            // Retirer la classe active de tous les items
            navItems.forEach(nav => nav.classList.remove('active'));
            
            // Ajouter la classe active à l'item cliqué
            this.classList.add('active');
            
            // Cacher toutes les sections
            sections.forEach(section => section.classList.remove('active'));
            
            // Afficher la section ciblée
            const target = document.getElementById(targetSection + '-section');
            if (target) {
                target.classList.add('active');
                // Scroll smooth vers le haut
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });
    });
});

// Modal de modification
function openEditModal(id, type, texte) {
    document.getElementById('edit-id').value = id;
    document.getElementById('edit-type').value = type;
    document.getElementById('edit-texte').value = texte;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Fermer le modal en cliquant en dehors
document.addEventListener('click', function(e) {
    const modal = document.getElementById('editModal');
    if (e.target === modal) {
        closeEditModal();
    }
});

// Fermer avec Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeEditModal();
    }
});
</script>
</body>
</html>