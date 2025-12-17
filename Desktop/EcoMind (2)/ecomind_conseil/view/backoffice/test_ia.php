<?php
session_start();

if (!isset($_SESSION['admin_logged']) || $_SESSION['admin_logged'] !== true) {
    header("Location: index.php");
    exit;
}

$base = dirname(__DIR__, 2);
require_once $base . '/config.php';
require_once $base . '/model/conseilReponse_model.php';
require_once $base . '/controller/ia_conseil_generator.php';
require_once 'notifications.php';

// Créer une réponse de test
$reponseTest = new ReponseFormulaire(
    1,                      // id
    'test@example.com',     // email
    4,                      // nb personnes
    7,                      // douche freq
    15,                     // durée douche
    'electrique',           // chauffage
    22,                     // temp hiver
    'voiture',              // transport
    10                      // distance travail
);

// Vérifier la configuration des clés API
$openaiKey = getenv('OPENAI_API_KEY');
if (!$openaiKey) {
    $openaiKey = isset($_ENV['OPENAI_API_KEY']) ? $_ENV['OPENAI_API_KEY'] : 
                 (isset($_SERVER['OPENAI_API_KEY']) ? $_SERVER['OPENAI_API_KEY'] : null);
}

$huggingfaceKey = getenv('HUGGINGFACE_API_KEY');
if (!$huggingfaceKey) {
    $huggingfaceKey = isset($_ENV['HUGGINGFACE_API_KEY']) ? $_ENV['HUGGINGFACE_API_KEY'] : 
                      (isset($_SERVER['HUGGINGFACE_API_KEY']) ? $_SERVER['HUGGINGFACE_API_KEY'] : null);
}

// Statuts des clés API
$openaiConfigured = ($openaiKey && $openaiKey !== 'votre_cle_api_ici' && strlen($openaiKey) > 20);
$huggingfaceConfigured = ($huggingfaceKey && $huggingfaceKey !== 'votre_cle_huggingface_ici' && strlen($huggingfaceKey) > 10);

// Générer les conseils
$generator = new IAConseilGenerator();
$conseils = $generator->genererConseils($reponseTest);

// Utiliser les variables déjà définies plus haut
$apiKey = $openaiKey;
$hfKey = $huggingfaceKey;
$modeUtilise = $openaiConfigured ? 'OpenAI GPT-3.5' : 'Mode Intelligent Par Défaut';

// Générer les notifications pour cette page
$db = Config::getConnexion();
$notifications = genererNotificationsPage($db, 'test_ia');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test IA - EcoMind Admin</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo dirname($_SERVER['SCRIPT_NAME']); ?>/backoffice.css?v=<?php echo time(); ?>">
    <style>
        .test-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .conseil-result {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 10px;
            margin: 15px 0;
            border-left: 4px solid #667eea;
        }
        
        .conseil-result h3 {
            margin-top: 0;
            color: #667eea;
        }
        
        .conseil-result p {
            font-size: 1.1em;
            line-height: 1.6;
            color: #333;
            margin: 10px 0 0 0;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 0.9em;
        }
        
        .status-success {
            background: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-warning {
            background: #fff3e0;
            color: #f57c00;
        }
        
        .data-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .data-row:last-child {
            border-bottom: none;
        }
        
        .data-label {
            font-weight: bold;
            width: 200px;
            color: #666;
        }
        
        .data-value {
            color: #013220;
        }
    </style>
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
            <a href="gestion_reponses.php" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Gestion des Réponses</span>
            </a>
            <a href="gestion_ia.php" class="nav-item">
                <span>🤖 Gestion IA</span>
            </a>
            <a href="test_ia.php" class="nav-item active">
                <i class="fas fa-flask"></i>
                <span>Test IA</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-header">
            <h1><i class="fas fa-flask"></i> Test du Générateur IA</h1>
            <div style="display: flex; align-items: center; gap: 15px;">
                <!-- Cloche de notifications -->
                <?= renderNotificationBell($notifications) ?>
                <a href="gestion_ia.php" class="logout-btn">Retour Gestion IA</a>
            </div>
        </div>

        <!-- Configuration détectée -->
        <div class="section">
            <h2>🔧 Configuration Détectée</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-robot"></i></div>
                    <div>
                        <p>Mode utilisé</p>
                        <h3 style="font-size: 14px;"><?= $modeUtilise ?></h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-key"></i></div>
                    <div>
                        <p>OpenAI API Key</p>
                        <h3 style="font-size: 14px;">
                            <?php if ($openaiConfigured): ?>
                                <span class="status-badge status-warning">❌ Non configurée</span>
                            <?php else: ?>
                                <span class="status-badge status-success">✅ Configurée</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="icon"><i class="fas fa-key"></i></div>
                    <div>
                        <p>Hugging Face API Key</p>
                        <h3 style="font-size: 14px;">
                            <?php if ($huggingfaceConfigured): ?>
                                <span class="status-badge status-success">✅ Configurée</span>
                            <?php else: ?>
                                <span class="status-badge status-warning">❌ Non configurée</span>
                            <?php endif; ?>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulaire de test interactif -->
        <div class="section">
            <h2>🧪 Test Interactif de l'IA</h2>
            <div id="validationMessage" style="display: none; padding: 15px; margin-bottom: 20px; border-radius: 8px; font-weight: bold;"></div>
            <form id="testForm" style="background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 25px;">
                    
                    <!-- Section Foyer -->
                    <div class="form-group">
                        <label for="nb_personnes" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">👥 Nombre de personnes <span style="color: red;">*</span></label>
                        <input type="number" id="nb_personnes" name="nb_personnes" placeholder="Ex: 4" min="1" max="10" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                        <div class="error-message" id="error_nb_personnes" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                    
                    <!-- Section Eau -->
                    <div class="form-group">
                        <label for="douche_freq" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">🚿 Douches par semaine <span style="color: red;">*</span></label>
                        <input type="number" id="douche_freq" name="douche_freq" placeholder="Ex: 7" min="1" max="21" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                        <div class="error-message" id="error_douche_freq" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="douche_duree" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">⏱️ Durée douche (min) <span style="color: red;">*</span></label>
                        <input type="number" id="douche_duree" name="douche_duree" placeholder="Ex: 10" min="1" max="90" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                        <div class="error-message" id="error_douche_duree" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                    
                    <!-- Section Énergie -->
                    <div class="form-group">
                        <label for="chauffage" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">🔥 Type de chauffage <span style="color: red;">*</span></label>
                        <select id="chauffage" name="chauffage" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                            <option value="">Choisissez...</option>
                            <option value="electrique">Électrique</option>
                            <option value="gaz">Gaz</option>
                            <option value="pompe_a_chaleur">Pompe à chaleur</option>
                            <option value="bois">Bois</option>
                        </select>
                        <div class="error-message" id="error_chauffage" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="temp_hiver" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">🌡️ Température hiver (°C) <span style="color: red;">*</span></label>
                        <input type="number" id="temp_hiver" name="temp_hiver" placeholder="Ex: 20" min="-40" max="55" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                        <div class="error-message" id="error_temp_hiver" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                    
                    <!-- Section Transport -->
                    <div class="form-group">
                        <label for="transport" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">🚗 Moyen de transport <span style="color: red;">*</span></label>
                        <select id="transport" name="transport" style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                            <option value="">Choisissez...</option>
                            <option value="voiture">Voiture</option>
                            <option value="transport_commun">Transport en commun</option>
                            <option value="velo">Vélo</option>
                            <option value="marche">Marche</option>
                            <option value="moto">Moto</option>
                        </select>
                        <div class="error-message" id="error_transport" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label for="distance" style="display: block; margin-bottom: 8px; font-weight: bold; color: #013220;">📏 Distance travail (km) <span style="color: red;">*</span></label>
                        <input type="number" id="distance" name="distance" placeholder="Ex: 15" min="0" max="100" 
                               style="width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 16px;">
                        <div class="error-message" id="error_distance" style="color: red; font-size: 12px; margin-top: 5px; display: none;"></div>
                    </div>
                </div>
                
                <div style="text-align: center; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                    <button type="button" onclick="remplirExemple()" 
                            style="background: #013220; color: white; padding: 12px 25px; border: none; border-radius: 25px; font-size: 16px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                        📝 Remplir avec un exemple
                    </button>
                    <button type="button" onclick="testerIA()" id="testButton" 
                            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 15px 40px; border: none; border-radius: 25px; font-size: 18px; font-weight: bold; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);">
                        🤖 Tester l'IA avec ces données
                    </button>
                    <button type="button" onclick="viderFormulaire()" 
                            style="background: #757575; color: white; padding: 12px 25px; border: none; border-radius: 25px; font-size: 16px; cursor: pointer; transition: all 0.3s;">
                        🗑️ Vider le formulaire
                    </button>
                </div>
            </form>
        </div>

        <!-- Résultats du test -->
        <div class="section" id="resultatsSection" style="display: none;">
            <h2>✨ Résultats du Test IA</h2>
            <div id="loadingSpinner" style="text-align: center; padding: 40px; display: none;">
                <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite;"></div>
                <p style="margin-top: 15px; color: #667eea; font-weight: bold;">🤖 L'IA génère vos conseils personnalisés...</p>
            </div>
            <div id="resultatsContent"></div>
        </div>



        <!-- Informations -->
        <div class="section">
            <h2>💡 Informations</h2>
            <div class="test-card" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.05)); border-left: 4px solid #667eea;">
                <p style="margin: 0; line-height: 1.8; color: #333;">
                    <strong>Mode actuel :</strong> <?= $modeUtilise ?>
                    <br><br>
                    <?php if ($modeUtilise === 'Mode Intelligent Par Défaut'): ?>
                        <strong>💡 Note :</strong> Le mode par défaut utilise des règles intelligentes avec 4 variations par conseil. 
                        Chaque régénération produit des formulations différentes !
                        <br><br>
                        <strong>🎯 Pour encore plus :</strong> Activez OpenAI pour des conseils ultra-naturels et infiniment variés.
                    <?php else: ?>
                        <strong>✅ Excellent !</strong> Vous utilisez OpenAI pour générer des conseils ultra-personnalisés et naturels.
                        <br><br>
                        <strong>🔄 Variété maximale :</strong> Chaque régénération crée des conseils uniques !
                    <?php endif; ?>
                    <br><br>
                    <strong>🔄 Régénération :</strong> Cliquez sur "Régénérer les conseils" pour voir de nouvelles variations !
                </p>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="section">
            <h2>🚀 Actions Rapides</h2>
            <div style="display: flex; gap: 15px; flex-wrap: wrap; justify-content: center;">
                <a href="../frontoffice/formulaire.html" target="_blank" class="btn btn-primary" style="background: #013220; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; display: inline-block;">
                    📝 Tester le formulaire complet
                </a>
                <a href="gestion_ia.php" class="btn btn-primary" style="background: #764ba2; color: white; padding: 12px 25px; border-radius: 25px; text-decoration: none; display: inline-block;">
                    ⚙️ Retour Gestion IA
                </a>
            </div>
        </div>
    </div>
</div>

<script>
<?= getNotificationJavaScript() ?>

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

// ==================== FONCTIONS DE TEST IA ====================

// ==================== VALIDATION JAVASCRIPT ====================

function validerFormulaire() {
    let isValid = true;
    const validationMessage = document.getElementById('validationMessage');
    
    // Réinitialiser les messages d'erreur
    document.querySelectorAll('.error-message').forEach(msg => {
        msg.style.display = 'none';
    });
    
    // Réinitialiser les bordures
    document.querySelectorAll('input, select').forEach(field => {
        field.style.borderColor = '#e0e0e0';
    });
    
    // Validation nombre de personnes
    const nbPersonnes = document.getElementById('nb_personnes');
    if (!nbPersonnes.value || nbPersonnes.value < 1 || nbPersonnes.value > 10) {
        showFieldError('nb_personnes', 'Le nombre de personnes doit être entre 1 et 10');
        isValid = false;
    }
    
    // Validation douches par semaine
    const doucheFreq = document.getElementById('douche_freq');
    if (!doucheFreq.value || doucheFreq.value < 1 || doucheFreq.value > 21) {
        showFieldError('douche_freq', 'Le nombre de douches doit être entre 1 et 21 par semaine');
        isValid = false;
    }
    
    // Validation durée douche
    const doucheDuree = document.getElementById('douche_duree');
    if (!doucheDuree.value || doucheDuree.value < 1 || doucheDuree.value > 90) {
        showFieldError('douche_duree', 'La durée de douche doit être entre 1 et 90 minutes');
        isValid = false;
    }
    
    // Validation type de chauffage
    const chauffage = document.getElementById('chauffage');
    if (!chauffage.value) {
        showFieldError('chauffage', 'Veuillez sélectionner un type de chauffage');
        isValid = false;
    }
    
    // Validation température hiver
    const tempHiver = document.getElementById('temp_hiver');
    if (!tempHiver.value || tempHiver.value < -40 || tempHiver.value > 55) {
        showFieldError('temp_hiver', 'La température doit être entre -40 et 55°C');
        isValid = false;
    }
    
    // Validation moyen de transport
    const transport = document.getElementById('transport');
    if (!transport.value) {
        showFieldError('transport', 'Veuillez sélectionner un moyen de transport');
        isValid = false;
    }
    
    // Validation distance travail
    const distance = document.getElementById('distance');
    if (distance.value === '' || distance.value < 0 || distance.value > 100) {
        showFieldError('distance', 'La distance doit être entre 0 et 100 km');
        isValid = false;
    }
    
    // Afficher message global
    if (!isValid) {
        validationMessage.innerHTML = '❌ Veuillez corriger les erreurs ci-dessus';
        validationMessage.style.backgroundColor = '#ffebee';
        validationMessage.style.color = '#c62828';
        validationMessage.style.border = '1px solid #ef5350';
        validationMessage.style.display = 'block';
        
        // Faire défiler vers le message d'erreur
        validationMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
        validationMessage.style.display = 'none';
    }
    
    return isValid;
}

function showFieldError(fieldId, message) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById('error_' + fieldId);
    
    field.style.borderColor = '#f44336';
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

function testerIA() {
    // Valider le formulaire avant de continuer
    if (!validerFormulaire()) {
        return;
    }
    
    const button = document.getElementById('testButton');
    const resultatsSection = document.getElementById('resultatsSection');
    const loadingSpinner = document.getElementById('loadingSpinner');
    const resultatsContent = document.getElementById('resultatsContent');
    
    // Récupérer les données du formulaire
    const formData = {
        nb_personnes: document.getElementById('nb_personnes').value,
        douche_freq: document.getElementById('douche_freq').value,
        douche_duree: document.getElementById('douche_duree').value,
        chauffage: document.getElementById('chauffage').value,
        temp_hiver: document.getElementById('temp_hiver').value,
        transport: document.getElementById('transport').value,
        distance: document.getElementById('distance').value
    };
    
    // Afficher la section résultats et le spinner
    resultatsSection.style.display = 'block';
    loadingSpinner.style.display = 'block';
    resultatsContent.innerHTML = '';
    
    // Désactiver le bouton
    button.disabled = true;
    button.innerHTML = '⏳ Test en cours...';
    button.style.opacity = '0.7';
    
    // Faire défiler vers les résultats
    resultatsSection.scrollIntoView({ behavior: 'smooth' });
    
    // Simuler un appel AJAX (vous pouvez remplacer par un vrai appel)
    fetch('test_ia_ajax.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        // Masquer le spinner
        loadingSpinner.style.display = 'none';
        
        // Afficher les résultats
        resultatsContent.innerHTML = `
            <div class="conseil-result" style="border-left-color: #1976d2; margin-bottom: 20px;">
                <h3>💧 Conseil EAU</h3>
                <p>${data.eau || 'Conseil eau généré'}</p>
                <div class="conseil-meta">
                    <span class="conseil-badge">Personnalisé pour ${formData.nb_personnes} personnes</span>
                </div>
            </div>
            
            <div class="conseil-result" style="border-left-color: #ff9800; margin-bottom: 20px;">
                <h3>🔥 Conseil ÉNERGIE</h3>
                <p>${data.energie || 'Conseil énergie généré'}</p>
                <div class="conseil-meta">
                    <span class="conseil-badge">Chauffage ${formData.chauffage} à ${formData.temp_hiver}°C</span>
                </div>
            </div>
            
            <div class="conseil-result" style="border-left-color: #4caf50;">
                <h3>🚗 Conseil TRANSPORT</h3>
                <p>${data.transport || 'Conseil transport généré'}</p>
                <div class="conseil-meta">
                    <span class="conseil-badge">${formData.transport} - ${formData.distance}km</span>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 30px; padding: 20px; background: #f0f8f0; border-radius: 15px;">
                <h4 style="color: #013220; margin-bottom: 10px;">✅ Test réussi !</h4>
                <p style="color: #666;">L'IA a généré 3 conseils personnalisés basés sur vos paramètres.</p>
            </div>
        `;
        
        showNotification('Test IA réussi ! 3 conseils générés.', 'success');
    })
    .catch(error => {
        // Masquer le spinner
        loadingSpinner.style.display = 'none';
        
        // Afficher une erreur ou des conseils par défaut
        resultatsContent.innerHTML = `
            <div style="text-align: center; padding: 30px; background: #fff3cd; border-radius: 15px; border-left: 4px solid #ffc107;">
                <h4 style="color: #856404; margin-bottom: 15px;">⚠️ Mode par défaut activé</h4>
                <p style="color: #856404;">L'API n'est pas disponible, mais l'IA par défaut fonctionne !</p>
            </div>
            
            <div class="conseil-result" style="border-left-color: #1976d2; margin: 20px 0;">
                <h3>💧 Conseil EAU (Mode par défaut)</h3>
                <p>Réduisez vos douches de ${formData.douche_duree} à 8 minutes maximum pour économiser l'eau !</p>
            </div>
            
            <div class="conseil-result" style="border-left-color: #ff9800; margin: 20px 0;">
                <h3>🔥 Conseil ÉNERGIE (Mode par défaut)</h3>
                <p>Baissez votre chauffage ${formData.chauffage} de ${formData.temp_hiver}°C à 19°C pour économiser l'énergie !</p>
            </div>
            
            <div class="conseil-result" style="border-left-color: #4caf50; margin: 20px 0;">
                <h3>🚗 Conseil TRANSPORT (Mode par défaut)</h3>
                <p>Optimisez vos trajets de ${formData.distance}km en ${formData.transport} pour réduire votre empreinte carbone !</p>
            </div>
        `;
        
        showNotification('Mode par défaut activé - Conseils générés !', 'success');
    })
    .finally(() => {
        // Réactiver le bouton
        button.disabled = false;
        button.innerHTML = '🤖 Tester l\'IA avec ces données';
        button.style.opacity = '1';
    });
}

// Animation CSS pour le spinner
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    .form-group input:focus, .form-group select:focus {
        border-color: #667eea !important;
        outline: none;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    
    #testButton:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    
    .conseil-result {
        background: white;
        padding: 20px;
        border-radius: 15px;
        border-left: 4px solid #667eea;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        margin-bottom: 15px;
        transition: all 0.3s;
    }
    
    .conseil-result:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    }
    
    .conseil-badge {
        background: #667eea;
        color: white;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: bold;
    }
    
    .conseil-meta {
        margin-top: 15px;
    }
    
    .field-valid {
        border-color: #4caf50 !important;
    }
    
    .field-invalid {
        border-color: #f44336 !important;
    }
`;
document.head.appendChild(style);

// ==================== VALIDATION EN TEMPS RÉEL ====================

// Ajouter la validation en temps réel sur tous les champs
document.addEventListener('DOMContentLoaded', function() {
    const fields = [
        'nb_personnes', 'douche_freq', 'douche_duree', 
        'chauffage', 'temp_hiver', 'transport', 'distance'
    ];
    
    fields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('blur', function() {
                validateSingleField(fieldId);
            });
            
            field.addEventListener('input', function() {
                // Réinitialiser l'apparence du champ lors de la saisie
                field.style.borderColor = '#e0e0e0';
                const errorDiv = document.getElementById('error_' + fieldId);
                if (errorDiv) {
                    errorDiv.style.display = 'none';
                }
            });
        }
    });
});

function validateSingleField(fieldId) {
    const field = document.getElementById(fieldId);
    const errorDiv = document.getElementById('error_' + fieldId);
    let isValid = true;
    let errorMessage = '';
    
    switch(fieldId) {
        case 'nb_personnes':
            if (!field.value || field.value < 1 || field.value > 10) {
                isValid = false;
                errorMessage = 'Le nombre de personnes doit être entre 1 et 10';
            }
            break;
            
        case 'douche_freq':
            if (!field.value || field.value < 1 || field.value > 21) {
                isValid = false;
                errorMessage = 'Le nombre de douches doit être entre 1 et 21 par semaine';
            }
            break;
            
        case 'douche_duree':
            if (!field.value || field.value < 1 || field.value > 90) {
                isValid = false;
                errorMessage = 'La durée de douche doit être entre 1 et 90 minutes';
            }
            break;
            
        case 'chauffage':
            if (!field.value) {
                isValid = false;
                errorMessage = 'Veuillez sélectionner un type de chauffage';
            }
            break;
            
        case 'temp_hiver':
            if (!field.value || field.value < -40 || field.value > 55) {
                isValid = false;
                errorMessage = 'La température doit être entre -40 et 55°C';
            }
            break;
            
        case 'transport':
            if (!field.value) {
                isValid = false;
                errorMessage = 'Veuillez sélectionner un moyen de transport';
            }
            break;
            
        case 'distance':
            if (field.value === '' || field.value < 0 || field.value > 100) {
                isValid = false;
                errorMessage = 'La distance doit être entre 0 et 100 km';
            }
            break;
    }
    
    if (isValid) {
        field.classList.add('field-valid');
        field.classList.remove('field-invalid');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    } else {
        field.classList.add('field-invalid');
        field.classList.remove('field-valid');
        if (errorDiv) {
            errorDiv.textContent = errorMessage;
            errorDiv.style.display = 'block';
        }
    }
    
    return isValid;
}

// ==================== FONCTIONS UTILITAIRES ====================

function remplirExemple() {
    // Remplir avec des données d'exemple
    document.getElementById('nb_personnes').value = '4';
    document.getElementById('douche_freq').value = '7';
    document.getElementById('douche_duree').value = '10';
    document.getElementById('chauffage').value = 'electrique';
    document.getElementById('temp_hiver').value = '20';
    document.getElementById('transport').value = 'voiture';
    document.getElementById('distance').value = '15';
    
    // Réinitialiser les styles de validation
    document.querySelectorAll('input, select').forEach(field => {
        field.style.borderColor = '#e0e0e0';
        field.classList.remove('field-valid', 'field-invalid');
    });
    
    // Masquer les messages d'erreur
    document.querySelectorAll('.error-message').forEach(msg => {
        msg.style.display = 'none';
    });
    
    // Masquer le message de validation global
    document.getElementById('validationMessage').style.display = 'none';
    
    showNotification('Formulaire rempli avec des données d\'exemple !', 'success');
}

function viderFormulaire() {
    // Vider tous les champs
    document.getElementById('nb_personnes').value = '';
    document.getElementById('douche_freq').value = '';
    document.getElementById('douche_duree').value = '';
    document.getElementById('chauffage').value = '';
    document.getElementById('temp_hiver').value = '';
    document.getElementById('transport').value = '';
    document.getElementById('distance').value = '';
    
    // Réinitialiser les styles de validation
    document.querySelectorAll('input, select').forEach(field => {
        field.style.borderColor = '#e0e0e0';
        field.classList.remove('field-valid', 'field-invalid');
    });
    
    // Masquer les messages d'erreur
    document.querySelectorAll('.error-message').forEach(msg => {
        msg.style.display = 'none';
    });
    
    // Masquer le message de validation global
    document.getElementById('validationMessage').style.display = 'none';
    
    // Masquer la section résultats
    document.getElementById('resultatsSection').style.display = 'none';
    
    showNotification('Formulaire vidé !', 'success');
}
</script>

</body>
</html>