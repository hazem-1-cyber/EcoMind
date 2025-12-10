<?php
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

require_once '../../config.php';

$db = Config::getConnexion();
$message = '';

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM conseil WHERE id = ?")->execute([$id]);
    $message = '<div class="success">Conseil supprimé avec succès !</div>';
}

// Modification
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['id'];
    $type = trim($_POST['type'] ?? '');
    $texte = trim($_POST['texte'] ?? '');
    
    if ($id && $type && $texte && in_array($type, ['eau','energie','transport'])) {
        $stmt = $db->prepare("UPDATE conseil SET type = ?, texte = ? WHERE id = ?");
        $stmt->execute([$type, $texte, $id]);
        $message = '<div class="success">Conseil modifié avec succès !</div>';
    } else {
        $message = '<div class="error">Erreur : données invalides.</div>';
    }
}

// Récupération de tous les conseils
$conseils = $db->query("SELECT * FROM conseil ORDER BY type, id DESC")->fetchAll(PDO::FETCH_OBJ);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Conseils - EcoMind</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="backoffice.css">
    <style>
        .conseils-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }
        
        .conseil-card {
            background: white;
            padding: 25px;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
            border-left: 4px solid #A8E6CF;
        }
        
        .conseil-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }
        
        .conseil-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .conseil-type {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .type-eau { background: #e3f2fd; color: #1976d2; }
        .type-energie { background: #fff3e0; color: #f57c00; }
        .type-transport { background: #e8f5e9; color: #388e3c; }
        
        .conseil-id {
            font-size: 13px;
            color: #999;
            font-weight: bold;
        }
        
        .conseil-texte {
            font-size: 15px;
            line-height: 1.6;
            color: #4a5568;
            margin-bottom: 20px;
            min-height: 60px;
        }
        
        .actions {
            display: flex;
            gap: 10px;
        }
        
        .back-link {
            display: inline-block;
            margin-bottom: 30px;
            color: #013220;
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.3s;
        }
        
        .back-link:hover {
            color: #A8E6CF;
            transform: translateX(-5px);
        }
        
        .stats-mini {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-mini {
            background: white;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            text-align: center;
        }
        
        .stat-mini h3 {
            font-size: 28px;
            color: #013220;
            margin-bottom: 5px;
        }
        
        .stat-mini p {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
        }
    </style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">
        <img src="../frontoffice/assets/images/Screenshot_2025-11-16_152042-removebg-preview.png" alt="EcoMind Logo">
    </div>
    <ul>
        <li class="nav-item" onclick="window.location.href='index.php'"><i class="fas fa-home"></i><span>Dashboard</span></li>
        <li class="nav-item active"><i class="fas fa-cog"></i><span>Gérer Conseils</span></li>
    </ul>
</div>

<!-- MAIN CONTENT -->
<div class="main-content">
    <div class="top-header">
        <h1><i class="fas fa-lightbulb"></i> Gérer les Conseils</h1>
        <a href="index.php" class="logout-btn">Retour au Dashboard</a>
    </div>

    <?= $message ?>

    <div class="stats-mini">
        <div class="stat-mini">
            <h3><?= count($conseils) ?></h3>
            <p>Total Conseils</p>
        </div>
        <div class="stat-mini">
            <h3><?= count(array_filter($conseils, fn($c) => $c->type === 'eau')) ?></h3>
            <p>💧 Eau</p>
        </div>
        <div class="stat-mini">
            <h3><?= count(array_filter($conseils, fn($c) => $c->type === 'energie')) ?></h3>
            <p>⚡ Énergie</p>
        </div>
        <div class="stat-mini">
            <h3><?= count(array_filter($conseils, fn($c) => $c->type === 'transport')) ?></h3>
            <p>🚗 Transport</p>
        </div>
    </div>

    <?php if ($conseils): ?>
    <div class="conseils-grid">
        <?php foreach ($conseils as $conseil): ?>
        <div class="conseil-card">
            <div class="conseil-header">
                <span class="conseil-type type-<?= $conseil->type ?>">
                    <?php
                    $icons = ['eau' => '💧', 'energie' => '⚡', 'transport' => '🚗'];
                    echo $icons[$conseil->type] . ' ' . strtoupper($conseil->type);
                    ?>
                </span>
                <span class="conseil-id">#<?= $conseil->id ?></span>
            </div>
            <div class="conseil-texte">
                <?= nl2br(htmlspecialchars($conseil->texte)) ?>
            </div>
            <div class="actions">
                <button class="btn btn-info" onclick="openEditModal(<?= $conseil->id ?>, '<?= htmlspecialchars($conseil->type, ENT_QUOTES) ?>', `<?= htmlspecialchars($conseil->texte, ENT_QUOTES) ?>`)">
                    <i class="fas fa-edit"></i> Modifier
                </button>
                <a href="?delete=<?= $conseil->id ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce conseil ?')">
                    <i class="fas fa-trash"></i> Supprimer
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
        <div class="section">
            <p style="text-align:center;color:#666;padding:60px;font-size:18px;">Aucun conseil dans la base de données</p>
        </div>
    <?php endif; ?>
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
document.getElementById('editModal').addEventListener('click', function(e) {
    if (e.target === this) {
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
