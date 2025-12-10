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

// Suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $db->prepare("DELETE FROM conseil WHERE idconseil = ?")->execute([$id]);
    $_SESSION['notification'] = 'Conseil supprimé avec succès !';
    header("Location: index.php");
    exit;
}

// Les notifications seront affichées par JavaScript

// Modification
if (isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = (int)$_POST['idconseil'];
    $type = trim($_POST['type'] ?? '');
    $texte = trim($_POST['texte'] ?? '');
    
    if ($id && $type && $texte && in_array($type, ['eau','energie','transport'])) {
        $stmt = $db->prepare("UPDATE conseil SET type = ?, texte = ? WHERE idconseil = ?");
        $stmt->execute([$type, $texte, $id]);
        $_SESSION['notification'] = 'Conseil modifié avec succès !';
        header("Location: gerer_conseils.php");
        exit;
    } else {
        $_SESSION['notification'] = 'Erreur : données invalides.';
        $_SESSION['notification_type'] = 'error';
        header("Location: gerer_conseils.php");
        exit;
    }
}

// === STATS ===
$totalReponses   = $db->query("SELECT COUNT(*) FROM reponse_formulaire")->fetchColumn();
$aujourdhui      = $db->query("SELECT COUNT(*) FROM reponse_formulaire WHERE DATE(date_soumission) = CURDATE()")->fetchColumn();
$totalConseils   = $db->query("SELECT COUNT(*) FROM conseil")->fetchColumn();
$totalPersonnes  = $db->query("SELECT SUM(nb_personnes) FROM reponse_formulaire")->fetchColumn() ?: 0;

// Récupération de tous les conseils
$conseils = $db->query("SELECT * FROM conseil ORDER BY type, idconseil DESC")->fetchAll(PDO::FETCH_OBJ);

// Générer les notifications pour cette page
$notifications = genererNotificationsPage($db, 'conseils');

// Page dédiée uniquement à la gestion des conseils
// Les réponses sont gérées dans gestion_reponses.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Conseils - EcoMind</title>
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
            <a href="gerer_conseils.php" class="nav-item active">
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
        <h1><i class="fas fa-lightbulb"></i> Gestion de Conseils</h1>
        <div style="display: flex; align-items: center; gap: 15px;">
            <!-- Cloche de notifications -->
            <?= renderNotificationBell($notifications) ?>
            <a href="index.php" class="logout-btn">Retour au Dashboard</a>
        </div>
    </div>



        <!-- STATISTIQUES -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon"><i class="fas fa-lightbulb"></i></div>
                <div>
                    <p>Total Conseils</p>
                    <h3><?= $totalConseils ?></h3>
                </div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05)); border-left: 4px solid #667eea;">
                <div class="icon" style="background: linear-gradient(135deg, #667eea, #764ba2); color: white;"><i class="fas fa-poll"></i></div>
                <div>
                    <p>Total Réponses</p>
                    <h3><?= $totalReponses ?></h3>
                </div>
                <a href="gestion_reponses.php" style="display: inline-block; margin-top: 10px; padding: 8px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 20px; font-size: 0.85em; transition: all 0.3s;">
                    Gérer les réponses →
                </a>
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
        </div>

        <!-- AJOUT NOUVEAU CONSEIL -->
        <div class="section" style="background: linear-gradient(135deg, rgba(168, 230, 207, 0.1), rgba(1, 50, 32, 0.05)); border-left: 4px solid #A8E6CF; margin-bottom: 30px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px;">
                <h2 style="margin: 0; color: #013220;"><i class="fas fa-plus-circle" style="color: #A8E6CF;"></i> Ajouter un nouveau conseil</h2>
                <button type="button" id="toggle-conseil-form-btn" class="btn" style="background: #A8E6CF; color: #013220; padding: 8px 15px; border-radius: 20px; border: none; cursor: pointer; font-weight: bold; transition: all 0.3s;">
                    <i class="fas fa-chevron-down"></i> Afficher le formulaire
                </button>
            </div>
            
            <div id="add-conseil-form-container" style="display: none; animation: slideDown 0.3s ease;">
                <form method="post" id="form-add-conseil" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                    <input type="hidden" name="action" value="add">
                    
                    <!-- Section Catégorie -->
                    <div class="form-section">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #A8E6CF;">
                            <i class="fas fa-tag"></i> Catégorie du conseil
                        </h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="conseil-type">🏷️ Type de conseil *</label>
                                <select name="type" id="conseil-type">
                                    <option value="">-- Sélectionner une catégorie --</option>
                                    <option value="eau">💧 Eau - Conseils d'économie d'eau</option>
                                    <option value="energie">⚡ Énergie - Conseils d'économie d'énergie</option>
                                    <option value="transport">🚗 Transport - Conseils de mobilité durable</option>
                                </select>
                                <span class="error-message" id="error-conseil-type"></span>
                                <span class="success-message" id="success-conseil-type"></span>
                                <small class="form-help">Choisissez la catégorie qui correspond le mieux à votre conseil</small>
                            </div>
                        </div>
                    </div>

                    <!-- Section Contenu -->
                    <div class="form-section">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #4FC3F7;">
                            <i class="fas fa-edit"></i> Contenu du conseil
                        </h3>
                        <div class="form-grid">
                            <div class="form-group" style="grid-column: 1 / -1;">
                                <label for="conseil-texte">📝 Texte du conseil *</label>
                                <textarea name="texte" id="conseil-texte" rows="6" placeholder="Rédigez votre conseil écologique ici...

Exemple pour la catégorie EAU :
Prenez des douches de 5 minutes maximum pour économiser jusqu'à 60L d'eau par douche. Installez un pommeau de douche économique pour réduire le débit sans perdre en confort.

Exemple pour la catégorie ÉNERGIE :
Baissez votre thermostat de 1°C pour économiser 7% sur votre facture de chauffage. Pensez à fermer les volets la nuit pour conserver la chaleur.

Exemple pour la catégorie TRANSPORT :
Privilégiez le covoiturage ou les transports en commun pour vos trajets quotidiens. Un trajet en bus émet 5 fois moins de CO2 qu'un trajet en voiture individuelle."></textarea>
                                <span class="error-message" id="error-conseil-texte"></span>
                                <span class="success-message" id="success-conseil-texte"></span>
                                <small class="form-help">Minimum 20 caractères - Soyez précis et donnez des conseils pratiques</small>
                                <div id="char-counter" style="text-align: right; color: #666; font-size: 12px; margin-top: 5px;">0 caractères</div>
                            </div>
                        </div>
                    </div>

                    <!-- Aperçu du conseil -->
                    <div class="form-section" id="conseil-preview-section" style="display: none;">
                        <h3 style="color: #013220; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #FFD54F;">
                            <i class="fas fa-eye"></i> Aperçu du conseil
                        </h3>
                        <div id="conseil-preview" style="background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 4px solid #A8E6CF;">
                            <div class="conseil-badge" id="preview-badge" style="display: inline-block; padding: 6px 12px; border-radius: 20px; font-weight: bold; font-size: 12px; margin-bottom: 10px;"></div>
                            <p id="preview-text" style="margin: 0; line-height: 1.6; color: #333;"></p>
                        </div>
                    </div>
                    
                    <!-- Boutons d'action -->
                    <div class="form-actions">
                        <button type="button" id="reset-conseil-form-btn" class="btn btn-secondary">
                            <i class="fas fa-undo"></i> Réinitialiser
                        </button>
                        <button type="button" id="preview-conseil-btn" class="btn" style="background: #4FC3F7; color: white; border: none; padding: 12px 25px; border-radius: 25px; cursor: pointer; font-weight: bold; transition: all 0.3s;">
                            <i class="fas fa-eye"></i> Aperçu
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-conseil-btn">
                            <i class="fas fa-plus-circle"></i> Ajouter le conseil
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
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            transition: all 0.3s;
            background: white;
            font-family: inherit;
            resize: vertical;
        }
        
        .form-group textarea {
            min-height: 120px;
            line-height: 1.6;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #A8E6CF;
            box-shadow: 0 0 0 3px rgba(168, 230, 207, 0.2);
        }
        
        .form-group input.error,
        .form-group select.error,
        .form-group textarea.error {
            border-color: #f44336;
            background: #ffebee;
        }
        
        .form-group input.success,
        .form-group select.success,
        .form-group textarea.success {
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
            flex-wrap: wrap;
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
        
        .conseil-badge {
            background: #A8E6CF;
            color: #013220;
        }
        
        .conseil-badge.eau {
            background: #4FC3F7;
            color: white;
        }
        
        .conseil-badge.energie {
            background: #FFD54F;
            color: #333;
        }
        
        .conseil-badge.transport {
            background: #81C784;
            color: white;
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

        <!-- GESTION DES CONSEILS -->
        <div class="conseils-section">
            <!-- Filtres -->
            <div class="section" style="padding:20px;margin-bottom:20px;">
                <div style="display:flex;gap:15px;align-items:center;flex-wrap:wrap;">
                    <label style="font-weight:600;color:#013220;">Filtrer par catégorie :</label>
                    <button class="filter-btn active" data-filter="all">
                        <i class="fas fa-globe"></i> Tous (<?= count($conseils) ?>)
                    </button>
                    <button class="filter-btn" data-filter="eau">
                        💧 Eau (<?= count(array_filter($conseils, fn($c) => $c->type === 'eau')) ?>)
                    </button>
                    <button class="filter-btn" data-filter="energie">
                        ⚡ Énergie (<?= count(array_filter($conseils, fn($c) => $c->type === 'energie')) ?>)
                    </button>
                    <button class="filter-btn" data-filter="transport">
                        🚗 Transport (<?= count(array_filter($conseils, fn($c) => $c->type === 'transport')) ?>)
                    </button>
                </div>
            </div>

            <?php if ($conseils): ?>
            <div class="section">
                <div class="conseils-grid">
                    <?php foreach ($conseils as $conseil): ?>
                    <div class="conseil-card" data-type="<?= $conseil->type ?>">
                        <div class="conseil-header">
                            <span class="conseil-badge conseil-badge-<?= $conseil->type ?>">
                                <?php
                                $icons = ['eau' => '💧', 'energie' => '⚡', 'transport' => '🚗'];
                                echo $icons[$conseil->type] . ' ' . strtoupper($conseil->type);
                                ?>
                            </span>
                            <span class="conseil-id">#<?= $conseil->idconseil ?></span>
                        </div>
                        <div class="conseil-body">
                            <p><?= nl2br(htmlspecialchars($conseil->texte)) ?></p>
                        </div>
                        <div class="conseil-actions">
                            <button class="btn btn-info" onclick="openEditModal(<?= $conseil->idconseil ?>, '<?= htmlspecialchars($conseil->type, ENT_QUOTES) ?>', `<?= htmlspecialchars($conseil->texte, ENT_QUOTES) ?>`)">
                                <i class="fas fa-edit"></i> Modifier
                            </button>
                            <a href="?delete=<?= $conseil->idconseil ?>" class="btn btn-danger" onclick="return confirm('Supprimer ce conseil ?')">
                                <i class="fas fa-trash"></i> Supprimer
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php else: ?>
                <div class="section">
                    <p style="text-align:center;color:#666;padding:60px;font-size:18px;">
                        <i class="fas fa-inbox" style="font-size:48px;display:block;margin-bottom:20px;opacity:0.3;"></i>
                        Aucun conseil dans la base de données
                    </p>
                </div>
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
            <input type="hidden" name="idconseil" id="edit-id">
            
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
                <button type="button" class="btn-cancel" onclick="closeEditModal()">Annuler</button>
                <button type="submit" class="btn-save"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
// ==================== GESTION DU FORMULAIRE D'AJOUT CONSEIL ====================

document.addEventListener('DOMContentLoaded', function() {
    const toggleConseilBtn = document.getElementById('toggle-conseil-form-btn');
    const conseilFormContainer = document.getElementById('add-conseil-form-container');
    const conseilForm = document.getElementById('form-add-conseil');
    const resetConseilBtn = document.getElementById('reset-conseil-form-btn');
    const previewBtn = document.getElementById('preview-conseil-btn');
    const previewSection = document.getElementById('conseil-preview-section');
    
    // Toggle du formulaire conseil
    if (toggleConseilBtn) {
        toggleConseilBtn.addEventListener('click', function() {
            if (conseilFormContainer.style.display === 'none') {
                conseilFormContainer.style.display = 'block';
                toggleConseilBtn.innerHTML = '<i class="fas fa-chevron-up"></i> Masquer le formulaire';
                toggleConseilBtn.style.background = '#f44336';
                toggleConseilBtn.style.color = 'white';
            } else {
                conseilFormContainer.style.display = 'none';
                toggleConseilBtn.innerHTML = '<i class="fas fa-chevron-down"></i> Afficher le formulaire';
                toggleConseilBtn.style.background = '#A8E6CF';
                toggleConseilBtn.style.color = '#013220';
            }
        });
    }
    
    // Réinitialiser le formulaire conseil
    if (resetConseilBtn) {
        resetConseilBtn.addEventListener('click', function() {
            conseilForm.reset();
            clearConseilValidation();
            updateCharCounter();
            previewSection.style.display = 'none';
        });
    }
    
    // Aperçu du conseil
    if (previewBtn) {
        previewBtn.addEventListener('click', function() {
            updateConseilPreview();
        });
    }
    
    // Validation en temps réel pour le conseil
    setupConseilRealTimeValidation();
    
    // Validation à la soumission du conseil
    if (conseilForm) {
        conseilForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (validateConseilForm()) {
                this.submit();
            }
        });
    }
    
    // Compteur de caractères
    updateCharCounter();
});

// ==================== FONCTIONS DE VALIDATION CONSEIL ====================

function setupConseilRealTimeValidation() {
    // Type de conseil
    const typeField = document.getElementById('conseil-type');
    if (typeField) {
        typeField.addEventListener('change', function() {
            validateConseilRequired(this.value, 'conseil-type', 'Le type de conseil');
            updateConseilPreview();
        });
    }
    
    // Texte du conseil
    const texteField = document.getElementById('conseil-texte');
    if (texteField) {
        texteField.addEventListener('input', function() {
            validateConseilTexte(this.value);
            updateCharCounter();
            updateConseilPreview();
        });
        
        texteField.addEventListener('blur', function() {
            validateConseilTexte(this.value);
        });
    }
}

function validateConseilRequired(value, fieldId, fieldName) {
    const field = document.getElementById(fieldId);
    const errorSpan = document.getElementById('error-' + fieldId);
    const successSpan = document.getElementById('success-' + fieldId);
    
    if (!value.trim()) {
        setConseilFieldError(field, errorSpan, successSpan, `❌ ${fieldName} est obligatoire`);
        return false;
    }
    
    setConseilFieldSuccess(field, errorSpan, successSpan, `✅ ${fieldName} sélectionné`);
    return true;
}

function validateConseilTexte(value) {
    const field = document.getElementById('conseil-texte');
    const errorSpan = document.getElementById('error-conseil-texte');
    const successSpan = document.getElementById('success-conseil-texte');
    
    if (!value.trim()) {
        setConseilFieldError(field, errorSpan, successSpan, '❌ Le texte du conseil est obligatoire');
        return false;
    }
    
    if (value.trim().length < 20) {
        setConseilFieldError(field, errorSpan, successSpan, `❌ Le conseil doit contenir au moins 20 caractères (${value.trim().length}/20)`);
        return false;
    }
    
    if (value.trim().length > 1000) {
        setConseilFieldError(field, errorSpan, successSpan, `❌ Le conseil ne peut pas dépasser 1000 caractères (${value.trim().length}/1000)`);
        return false;
    }
    
    setConseilFieldSuccess(field, errorSpan, successSpan, `✅ Conseil valide (${value.trim().length} caractères)`);
    return true;
}

function setConseilFieldError(field, errorSpan, successSpan, message) {
    field.classList.remove('success');
    field.classList.add('error');
    errorSpan.textContent = message;
    successSpan.textContent = '';
}

function setConseilFieldSuccess(field, errorSpan, successSpan, message) {
    field.classList.remove('error');
    field.classList.add('success');
    errorSpan.textContent = '';
    successSpan.textContent = message;
}

function setConseilFieldNeutral(field, errorSpan, successSpan) {
    field.classList.remove('error', 'success');
    errorSpan.textContent = '';
    successSpan.textContent = '';
}

function clearConseilValidation() {
    const fields = ['conseil-type', 'conseil-texte'];
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        const errorSpan = document.getElementById('error-' + fieldId);
        const successSpan = document.getElementById('success-' + fieldId);
        if (field && errorSpan && successSpan) {
            setConseilFieldNeutral(field, errorSpan, successSpan);
        }
    });
}

function validateConseilForm() {
    let isValid = true;
    
    // Validation complète
    const typeValue = document.getElementById('conseil-type').value;
    const texteValue = document.getElementById('conseil-texte').value;
    
    if (!validateConseilRequired(typeValue, 'conseil-type', 'Le type de conseil')) isValid = false;
    if (!validateConseilTexte(texteValue)) isValid = false;
    
    if (!isValid) {
        showNotification('❌ Veuillez corriger les erreurs dans le formulaire', 'error');
        // Scroll vers la première erreur
        const firstError = document.querySelector('#form-add-conseil .form-group input.error, #form-add-conseil .form-group select.error, #form-add-conseil .form-group textarea.error');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
    } else {
        showNotification('✅ Conseil valide, ajout en cours...', 'success');
    }
    
    return isValid;
}

// ==================== FONCTIONS UTILITAIRES ====================

function updateCharCounter() {
    const texteField = document.getElementById('conseil-texte');
    const counter = document.getElementById('char-counter');
    if (texteField && counter) {
        const length = texteField.value.length;
        counter.textContent = `${length} caractères`;
        
        if (length < 20) {
            counter.style.color = '#f44336';
        } else if (length > 800) {
            counter.style.color = '#ff9800';
        } else {
            counter.style.color = '#4caf50';
        }
    }
}

function updateConseilPreview() {
    const typeField = document.getElementById('conseil-type');
    const texteField = document.getElementById('conseil-texte');
    const previewSection = document.getElementById('conseil-preview-section');
    const previewBadge = document.getElementById('preview-badge');
    const previewText = document.getElementById('preview-text');
    
    if (!typeField || !texteField || !previewSection) return;
    
    const type = typeField.value;
    const texte = texteField.value.trim();
    
    if (type && texte) {
        previewSection.style.display = 'block';
        
        // Mise à jour du badge
        const typeLabels = {
            'eau': '💧 EAU',
            'energie': '⚡ ÉNERGIE', 
            'transport': '🚗 TRANSPORT'
        };
        
        previewBadge.textContent = typeLabels[type] || type.toUpperCase();
        previewBadge.className = `conseil-badge ${type}`;
        
        // Mise à jour du texte
        previewText.textContent = texte;
        
        // Animation d'apparition
        previewSection.style.animation = 'slideDown 0.3s ease';
    } else {
        previewSection.style.display = 'none';
    }
}

// ==================== MODAL DE MODIFICATION ====================

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

// ==================== PAGE DÉDIÉE AUX CONSEILS ====================
// Les réponses sont maintenant gérées dans gestion_reponses.php

<?= getNotificationJavaScript() ?>

// ==================== FILTRES DE CONSEILS ====================

const filterBtns = document.querySelectorAll('.filter-btn');
const conseilCards = document.querySelectorAll('.conseil-card');

filterBtns.forEach(btn => {
    btn.addEventListener('click', function() {
        const filter = this.getAttribute('data-filter');
        
        // Retirer active de tous les filtres
        filterBtns.forEach(b => b.classList.remove('active'));
        this.classList.add('active');
        
        // Filtrer les cartes
        conseilCards.forEach(card => {
            if (filter === 'all' || card.getAttribute('data-type') === filter) {
                card.style.display = 'flex';
                card.style.animation = 'fadeIn 0.4s ease';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
</body>
</html>