<?php
// Charger les associations statiques depuis la base de données
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../controller/config/SettingsManager.php';

$db = Config::getConnexion();
$settingsManager = new SettingsManager();

// Récupérer le montant minimum depuis les paramètres
$minDonationAmount = $settingsManager->getMinDonationAmount();
$currency = $settingsManager->getCurrency();

// Charger les associations
$stmt = $db->query("SELECT id, nom FROM associations ORDER BY nom ASC");
$associations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Charger les catégories (types de don) dynamiquement
$stmtCategories = $db->query("SELECT id, nom, code, description FROM categories ORDER BY nom ASC");
$categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);

// Configuration pour le header
$pageTitle = "EcoMind - Faire un don";
$additionalCSS = ['style.css'];
$additionalJS = ['assets/js/don.js'];

// Inclure le header
include __DIR__ . '/includes/header.php';
?>

  <!-- Configuration temps réel pour le chatbot -->
  <script>
    window.CHATBOT_CONFIG = <?= json_encode([
      'minDonationAmount' => $minDonationAmount,
      'currency' => $currency,
      'autoValidate' => $settingsManager->isAutoValidateEnabled(),
      'notificationsEnabled' => $settingsManager->get('notifications_enabled', true)
    ]) ?>;
  </script>

  <!-- Inclure les fichiers du chatbot -->
  <link rel="stylesheet" href="assets/css/chatbot.css">
  <script src="assets/js/chatbot.js"></script>
  <script src="assets/js/image-upload.js"></script>

  <!-- Script pour les feuilles qui tombent -->
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    const leavesContainer = document.getElementById('fallingLeaves');
    const leafEmojis = ['🌿'];
    const numberOfLeaves = 25;

    function createLeaf() {
      const leaf = document.createElement('div');
      leaf.className = 'leaf';
      leaf.textContent = leafEmojis[Math.floor(Math.random() * leafEmojis.length)];
      
      // Position aléatoire horizontale
      leaf.style.left = Math.random() * 100 + '%';
      
      // Durée d'animation aléatoire (entre 8 et 15 secondes)
      const duration = Math.random() * 7 + 8;
      leaf.style.animationDuration = duration + 's';
      
      // Délai aléatoire avant le début
      const delay = Math.random() * 5;
      leaf.style.animationDelay = delay + 's';
      
      // Taille aléatoire
      const size = Math.random() * 20 + 20;
      leaf.style.fontSize = size + 'px';
      
      leavesContainer.appendChild(leaf);
      
      // Supprimer la feuille après l'animation
      setTimeout(() => {
        leaf.remove();
        createLeaf(); // Créer une nouvelle feuille
      }, (duration + delay) * 1000);
    }

    // Créer les feuilles initiales
    for (let i = 0; i < numberOfLeaves; i++) {
      setTimeout(() => createLeaf(), i * 300);
    }
  });
  </script>

  <!-- JavaScript simple pour les champs conditionnels -->
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Gestion des champs conditionnels
      const typeSelect = document.getElementById('type-don');
      if (typeSelect) {
        typeSelect.addEventListener('change', function() {
          const value = this.value;
          const moneyFields = document.getElementById('money-fields');
          const autreFields = document.getElementById('autre-type-fields');
          
          // Cacher tous les champs
          if (moneyFields) moneyFields.style.display = 'none';
          if (autreFields) autreFields.style.display = 'none';
          
          // Afficher les bons champs
          if (value === 'money' && moneyFields) {
            moneyFields.style.display = 'block';
          } else if (value && value !== 'money' && autreFields) {
            autreFields.style.display = 'block';
          }
        });
      }

    });
  </script>

  <!-- Animation de feuilles qui tombent -->
  <div class="falling-leaves" id="fallingLeaves"></div>

  <!-- CSS pour la section objectif et animations -->
  <style>
    /* Animation des feuilles qui tombent */
    .falling-leaves {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
      overflow: hidden;
    }

    .leaf {
      position: absolute;
      top: -50px;
      font-size: 30px;
      animation: fall linear infinite;
      opacity: 0.8;
    }

    @keyframes fall {
      0% {
        transform: translateY(-50px) rotate(0deg);
        opacity: 1;
      }
      100% {
        transform: translateY(100vh) rotate(360deg);
        opacity: 0.3;
      }
    }

    @keyframes sway {
      0%, 100% {
        transform: translateX(0);
      }
      50% {
        transform: translateX(30px);
      }
    }
  
  <style>
    .objectif-section {
      background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
      border: 2px solid #A8E6CF;
      border-radius: 20px;
      padding: 30px;
      margin-bottom: 30px;
      box-shadow: 0 10px 30px rgba(44, 95, 45, 0.15);
      animation: slideInDown 0.6s ease-out;
    }

    @keyframes slideInDown {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .objectif-header {
      text-align: center;
      margin-bottom: 25px;
    }

    .objectif-header h2 {
      color: #2c5f2d;
      font-size: 28px;
      margin: 0 0 10px 0;
      font-weight: 700;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    .objectif-header p {
      color: #6c757d;
      font-size: 16px;
      margin: 0;
    }

    .objectif-progress {
      margin: 25px 0;
    }

    .progress-info {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 12px;
    }

    .progress-label {
      font-weight: 600;
      color: #2c5f2d;
      font-size: 16px;
    }

    .progress-amount {
      font-weight: 700;
      color: #2c5f2d;
      font-size: 20px;
    }

    /* Styles originaux restaurés */

    .progress-message {
      margin-top: 15px;
      padding: 15px 20px;
      border-radius: 12px;
      font-size: 15px;
      font-weight: 600;
      text-align: center;
      animation: fadeIn 1s ease-out;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    .message-success {
      background: linear-gradient(135deg, #d4edda, #c3e6cb);
      color: #155724;
      border: 2px solid #28a745;
    }

    .message-warning {
      background: linear-gradient(135deg, #fff3cd, #ffeaa7);
      color: #856404;
      border: 2px solid #ffc107;
    }

    .message-info {
      background: linear-gradient(135deg, #d1ecf1, #bee5eb);
      color: #0c5460;
      border: 2px solid #17a2b8;
    }

    .objectif-stats {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
      gap: 15px;
      margin-top: 25px;
    }

    .stat-box {
      background: linear-gradient(135deg, #ffffff 0%, #f8fff9 100%);
      padding: 25px 20px;
      border-radius: 16px;
      text-align: center;
      box-shadow: 0 6px 20px rgba(44, 95, 45, 0.12);
      border: 2px solid rgba(168, 230, 207, 0.6);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .stat-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 3px;
      background: linear-gradient(90deg, #A8E6CF, #88b04b);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .stat-box:hover::before {
      transform: scaleX(1);
    }

    .stat-box:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 12px 30px rgba(44, 95, 45, 0.25);
      border-color: #88b04b;
    }

    .stat-value {
      font-size: 32px;
      font-weight: 700;
      color: #2c5f2d;
      margin-bottom: 8px;
      text-shadow: 0 2px 4px rgba(44, 95, 45, 0.1);
      animation: countUp 2s ease-out;
    }

    @keyframes countUp {
      from { 
        opacity: 0;
        transform: translateY(20px) scale(0.8);
      }
      to { 
        opacity: 1;
        transform: translateY(0) scale(1);
      }
    }

    .stat-icon {
      font-size: 32px;
      margin-bottom: 10px;
      animation: bounce 2s ease-in-out infinite;
      filter: drop-shadow(0 2px 4px rgba(44, 95, 45, 0.2));
    }

    .stat-label {
      font-size: 15px;
      color: #4a5f4a;
      font-weight: 600;
      text-align: center;
    }

    .encouragement-icon {
      font-size: 40px;
      animation: bounce 2s infinite;
    }

    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    
    /* Styles pour l'upload d'image */
    .image-upload-container {
      position: relative;
    }
    
    .image-upload-box {
      border: 3px dashed #A8E6CF;
      border-radius: 12px;
      padding: 40px;
      text-align: center;
      cursor: pointer;
      transition: all 0.3s;
      background: linear-gradient(135deg, #ffffff 0%, #f0fff4 100%);
    }
    
    .image-upload-box:hover {
      border-color: #88b04b;
      background: linear-gradient(135deg, #f0fff4 0%, #e8f5e9 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(44, 95, 45, 0.15);
    }
    
    .image-preview {
      position: relative;
      border-radius: 12px;
      overflow: hidden;
      border: 2px solid #A8E6CF;
      max-width: 100%;
    }
    
    .image-preview img {
      width: 100%;
      height: auto;
      display: block;
      max-height: 400px;
      object-fit: contain;
      background: #f8f9fa;
    }
    
    .remove-image {
      position: absolute;
      top: 10px;
      right: 10px;
      background: #dc3545;
      color: white;
      border: none;
      border-radius: 50%;
      width: 36px;
      height: 36px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 18px;
      transition: all 0.3s;
      box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
    }
    
    .remove-image:hover {
      background: #c82333;
      transform: scale(1.1);
    }

    /* Styles simples pour le formulaire */
    .don-container {
      position: relative;
      z-index: 2;
    }

    /* Styles simples pour la carte de formulaire */
    .form-card {
      background: white;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      border: 1px solid #e9ecef;
    }

    .form-card h1 {
      color: #2c5f2d;
      text-align: center;
      margin-bottom: 20px;
    }

    /* Styles simples pour les champs de formulaire */
    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      margin-bottom: 8px;
      color: #2c5f2d;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 2px solid #A8E6CF;
      border-radius: 8px;
      font-size: 16px;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #88b04b;
    }

    /* Styles simples pour les boutons de montant */
    .amounts {
      display: flex;
      gap: 10px;
      flex-wrap: wrap;
      margin-top: 10px;
    }

    .amount-btn {
      flex: 1;
      min-width: 80px;
      padding: 12px;
      text-align: center;
      background: rgba(168, 230, 207, 0.3);
      border: 2px solid #A8E6CF;
      border-radius: 8px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .amount-btn:hover,
    .amount-btn.selected {
      background: #A8E6CF;
      color: #013220;
      border-color: #013220;
    }

    /* === BOUTON SUBMIT HARMONISÉ === */
    .submit-btn {
      width: 100%;
      padding: 16px;
      background: linear-gradient(135deg, #013220 0%, rgba(1, 50, 32, 0.9) 50%, #A8E6CF 100%);
      color: white;
      border: none;
      border-radius: 50px;
      font-size: 18px;
      font-weight: 600;
      cursor: pointer;
      margin-top: 25px;
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      grid-column: span 2;
      box-shadow: 0 8px 25px rgba(168, 230, 207, 0.4);
      position: relative;
      overflow: hidden;
    }

    .submit-btn::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.3);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .submit-btn:hover::before {
      width: 400px;
      height: 400px;
    }

    .submit-btn:hover {
      transform: scale(1.05) translateY(-2px);
      box-shadow: 0 12px 35px rgba(168, 230, 207, 0.5);
    }

    .submit-btn:active {
      transform: scale(0.98);
    }

    .submit-btn:disabled {
      background: #ccc;
      cursor: not-allowed;
      transform: none;
      box-shadow: none;
    }

    /* === ANIMATIONS D'APPARITION === */
    .form-group {
      animation: slideInLeft 0.6s ease-out;
      animation-fill-mode: both;
    }

    .form-group:nth-child(1) { animation-delay: 0.1s; }
    .form-group:nth-child(2) { animation-delay: 0.2s; }
    .form-group:nth-child(3) { animation-delay: 0.3s; }
    .form-group:nth-child(4) { animation-delay: 0.4s; }
    .form-group:nth-child(5) { animation-delay: 0.5s; }

    @keyframes slideInLeft {
      from {
        opacity: 0;
        transform: translateX(-30px);
      }
      to {
        opacity: 1;
        transform: translateX(0);
      }
    }

    /* === EFFETS DE VALIDATION === */
    .form-group.valid input,
    .form-group.valid select,
    .form-group.valid textarea {
      border-color: #28a745;
      box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.1);
    }

    .form-group.valid label::after {
      content: '✓';
      position: absolute;
      right: -25px;
      top: 50%;
      transform: translateY(-50%);
      color: #28a745;
      font-weight: bold;
      font-size: 18px;
      animation: checkmark 0.5s ease-out;
    }

    @keyframes checkmark {
      from {
        opacity: 0;
        transform: translateY(-50%) scale(0);
      }
      to {
        opacity: 1;
        transform: translateY(-50%) scale(1);
      }
    }

    .form-group.error input,
    .form-group.error select,
    .form-group.error textarea {
      border-color: #dc3545;
      box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
      animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
      0%, 100% { transform: translateX(0); }
      25% { transform: translateX(-5px); }
      75% { transform: translateX(5px); }
    }

    /* === LOADING STATES === */
    .submit-btn.loading {
      pointer-events: none;
      opacity: 0.8;
    }

    .submit-btn.loading::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 20px;
      height: 20px;
      border: 2px solid rgba(255, 255, 255, 0.3);
      border-top: 2px solid white;
      border-radius: 50%;
      transform: translate(-50%, -50%);
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: translate(-50%, -50%) rotate(0deg); }
      100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* === MICRO-INTERACTIONS === */
    .form-group:hover label {
      color: #88b04b;
      transform: translateX(5px);
    }

    .form-card:hover::before {
      height: 8px;
      box-shadow: 0 0 20px rgba(168, 230, 207, 0.5);
    }

    /* === RESPONSIVE DYNAMIQUE === */
    @media (max-width: 768px) {
      .amounts {
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
      }
      
      .amount-btn {
        padding: 15px 12px;
        font-size: 14px;
      }
      
      .form-card {
        border-radius: 20px;
        margin: 10px;
      }
      
      .form-group input:focus,
      .form-group select:focus,
      .form-group textarea:focus {
        transform: translateY(-2px) scale(1.01);
      }
    }
  </style>

  <div class="don-container">
    <?php
    // Calculer les statistiques pour l'objectif
    require_once __DIR__ . '/../../controller/DonController.php';
    $donCtrl = new DonController();
    $allDons = $donCtrl->listDons()->fetchAll();
    
    $objectifMensuel = $settingsManager->get('objectif_mensuel', 10000);
    $totalCollecte = 0;
    $nombreDons = 0;
    
    // Calculer le total collecté ce mois (seulement les dons validés)
    $currentMonth = date('Y-m');
    foreach ($allDons as $don) {
      if ($don['type_don'] === 'money' && $don['montant'] && 
          $don['statut'] === 'validated' &&
          strpos($don['created_at'], $currentMonth) === 0) {
        $totalCollecte += $don['montant'];
        $nombreDons++;
      }
    }
    
    $pourcentageObjectif = min(100, round(($totalCollecte / $objectifMensuel) * 100));
    $restant = max(0, $objectifMensuel - $totalCollecte);
    
    // Messages d'encouragement selon le pourcentage
    $messageClass = 'message-info';
    $icon = '🌱';
    $message = '';
    
    if ($pourcentageObjectif >= 100) {
      $messageClass = 'message-success';
      $icon = '🎉';
      $message = "Objectif atteint ! Merci à tous nos généreux donateurs ! Ensemble, nous faisons la différence !";
    } elseif ($pourcentageObjectif >= 75) {
      $messageClass = 'message-success';
      $icon = '🚀';
      $message = "Incroyable ! Nous sommes presque à l'objectif ! Plus que " . number_format($restant, 2) . " TND !";
    } elseif ($pourcentageObjectif >= 50) {
      $messageClass = 'message-warning';
      $icon = '💪';
      $message = "Bravo ! Nous avons dépassé la moitié de l'objectif ! Continuons ensemble !";
    } elseif ($pourcentageObjectif >= 25) {
      $messageClass = 'message-info';
      $icon = '🌟';
      $message = "Excellent départ ! Chaque don compte pour protéger notre environnement !";
    } else {
      $messageClass = 'message-info';
      $icon = '🌱';
      $message = "Aidez-nous à atteindre notre objectif ! Votre générosité fait la différence !";
    }
    ?>
    
    <!-- Section Objectif du mois -->
    <div class="objectif-section">
      <div class="objectif-header">
        <h2>
          <span class="encouragement-icon"><?= $icon ?></span>
          Objectif du mois
        </h2>
        <p>Ensemble, protégeons notre environnement</p>
      </div>
      
      <div class="objectif-progress">
        <div class="progress-info">
          <span class="progress-label">Progression</span>
          <span class="progress-amount">
            <?= number_format($totalCollecte, 2) ?> / <?= number_format($objectifMensuel, 2) ?> TND
          </span>
        </div>
        
        <div style="background: #e9ecef; height: 40px; border-radius: 20px; overflow: hidden; position: relative;">
          <div style="background: linear-gradient(90deg, #2c5f2d, #88b04b, #A8E6CF); height: 100%; width: <?= $pourcentageObjectif ?>%; transition: width 1s ease; display: flex; align-items: center; justify-content: flex-end; padding-right: 15px; color: white; font-weight: 700; font-size: 16px;">
            <?= $pourcentageObjectif ?>%
          </div>
        </div>
        
        <div class="progress-message <?= $messageClass ?>">
          <?= $message ?>
        </div>
      </div>
      
      <div class="objectif-stats">
        <div class="stat-box">
          <div class="stat-icon">🎯</div>
          <div class="stat-value"><?= number_format($restant, 0) ?> TND</div>
          <div class="stat-label">Encore à collecter</div>
        </div>
        
        <div class="stat-box">
          <div class="stat-icon">🌳</div>
          <div class="stat-value"><?= $nombreDons > 0 ? $nombreDons * 10 : 0 ?></div>
          <div class="stat-label">Arbres sauvés</div>
        </div>
      </div>
      
      <!-- Messages d'encouragement -->
      <div class="encouragement-messages" style="margin-top: 25px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
        <div class="encouragement-card" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9); padding: 20px; border-radius: 15px; border-left: 4px solid #4CAF50; box-shadow: 0 4px 12px rgba(76, 175, 80, 0.15);">
          <div style="font-size: 32px; margin-bottom: 10px;">💚</div>
          <h3 style="color: #2c5f2d; font-size: 16px; margin-bottom: 8px; font-weight: 700;">Chaque geste compte</h3>
          <p style="color: #4a5f4a; font-size: 14px; margin: 0; line-height: 1.5;">Votre don, petit ou grand, fait une vraie différence pour notre planète.</p>
        </div>
        
        <div class="encouragement-card" style="background: linear-gradient(135deg, #fff3e0, #ffe0b2); padding: 20px; border-radius: 15px; border-left: 4px solid #FF9800; box-shadow: 0 4px 12px rgba(255, 152, 0, 0.15);">
          <div style="font-size: 32px; margin-bottom: 10px;">🌍</div>
          <h3 style="color: #e65100; font-size: 16px; margin-bottom: 8px; font-weight: 700;">Impact immédiat</h3>
          <p style="color: #5d4037; font-size: 14px; margin: 0; line-height: 1.5;">Rejoignez notre communauté de donneurs engagés pour l'environnement.</p>
        </div>
      </div>
      
      <!-- Message de remerciement EcoMind -->
      <div class="thank-you-message" style="margin-top: 30px; background: linear-gradient(135deg, #A8E6CF 0%, #88b04b 100%); padding: 35px; border-radius: 20px; text-align: center; box-shadow: 0 8px 25px rgba(44, 95, 45, 0.25); position: relative; overflow: hidden;">
        <!-- Feuilles décoratives -->
        <div style="position: absolute; top: -10px; left: 20px; font-size: 40px; opacity: 0.3; transform: rotate(-15deg);">🌿</div>
        <div style="position: absolute; top: 20px; right: 30px; font-size: 35px; opacity: 0.3; transform: rotate(25deg);">🍃</div>
        <div style="position: absolute; bottom: 15px; left: 40px; font-size: 30px; opacity: 0.3; transform: rotate(45deg);">🌱</div>
        <div style="position: absolute; bottom: -5px; right: 50px; font-size: 38px; opacity: 0.3; transform: rotate(-20deg);">🌿</div>
        
        <!-- Contenu principal -->
        <div style="position: relative; z-index: 1;">
          <div style="font-size: 56px; margin-bottom: 15px; animation: bounce 2s ease-in-out infinite;">💚</div>
          <h2 style="color: #ffffff; font-size: 32px; font-weight: 700; margin: 0 0 15px 0; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            Merci pour votre générosité !
          </h2>
          <p style="color: #ffffff; font-size: 18px; margin: 0; line-height: 1.6; font-weight: 500; text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
            Votre contribution fait une réelle différence pour notre planète.<br>
            Ensemble, construisons un avenir plus vert et durable ! 🌍
          </p>
          
          <!-- Badge de reconnaissance -->
          <div style="margin-top: 25px; display: inline-block; background: rgba(255, 255, 255, 0.25); padding: 12px 25px; border-radius: 50px; backdrop-filter: blur(10px); border: 2px solid rgba(255, 255, 255, 0.4);">
            <span style="color: #ffffff; font-size: 16px; font-weight: 600;">
              ✨ Vous êtes un héros de l'environnement ✨
            </span>
          </div>
        </div>
      </div>
      
      <style>
      @keyframes bounce {
        0%, 100% {
          transform: translateY(0) scale(1);
        }
        50% {
          transform: translateY(-10px) scale(1.1);
        }
      }
      </style>
    </div>

    <div class="form-card">
      <h1>Faire un don</h1>
      <p class="subtitle">Soutenez une association tunisienne engagée pour l'environnement.</p>

      <form id="don-form" action="verification.php" method="POST" enctype="multipart/form-data" novalidate>
        <div class="form-grid">

          <!-- Type de don -->
          <div class="form-group full">
            <label for="type-don">Type de don *</label>
            <select id="type-don" name="type_don">
              <option value="">Sélectionner un type de don</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat['code']) ?>" 
                        title="<?= htmlspecialchars($cat['description'] ?? '') ?>">
                  <?= htmlspecialchars($cat['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <span class="error-msg" id="type-don_error"></span>
            

          </div>

          <!-- Champs Money -->
          <div id="money-fields" style="display: none;">
            
            <!-- Montant -->
            <div class="form-group full">
              <label>Montant du don (<?= $currency ?>) *</label>
              <div class="amounts">
                <div class="amount-btn" data-value="50">50 <?= $currency ?></div>
                <div class="amount-btn" data-value="100">100 <?= $currency ?></div>
                <div class="amount-btn" data-value="200">200 <?= $currency ?></div>
                <div class="amount-btn" data-value="500">500 <?= $currency ?></div>
              </div>
              <div class="custom-amount">
                <input type="text" id="custom-amount" name="montant" placeholder="Autre montant (ex: 75)" data-min="<?= $minDonationAmount ?>">
              </div>
              <span class="error-msg" id="montant_error" style="display: block; margin-top: 8px;"></span>
            </div>

            <!-- Champ caché pour forcer le paiement en ligne -->
            <input type="hidden" name="livraison" value="en_ligne">
          </div>

          <!-- Champs Autre type -->
          <div id="autre-type-fields" style="display: none;">
            <div class="form-group">
              <label for="ville-autre">Ville *</label>
              <input type="text" id="ville-autre" name="ville" placeholder="Tunis">
              <span class="error-msg" id="ville-autre_error"></span>
            </div>
            
            <div class="form-group">
              <label for="cp-autre">Code postal *</label>
              <input type="text" id="cp-autre" name="cp" placeholder="1000">
              <span class="error-msg" id="cp-autre_error"></span>
            </div>
            
            <div class="form-group full">
              <label for="localisation-autre">Localisation</label>
              <input type="text" id="localisation-autre" name="localisation" placeholder="Coller le lien Google Maps">
              <span class="error-msg" id="localisation-autre_error"></span>
            </div>
            
            <div class="form-group">
              <label for="tel-autre">Téléphone *</label>
              <input type="text" id="tel-autre" name="tel" placeholder="98765432">
              <span class="error-msg" id="tel-autre_error"></span>
            </div>
            
            <div class="form-group full">
              <label for="description-don">Description du don *</label>
              <textarea id="description-don" name="description_don" rows="4" placeholder="Décrivez votre don..."></textarea>
              <span class="error-msg" id="description_error"></span>
            </div>
            
            <div class="form-group full">
              <label for="image-don">📸 Photo du don (optionnel)</label>
              <div class="image-upload-container">
                <input type="file" id="image-don" name="image_don" accept="image/*" style="display: none;">
                <div class="image-upload-box" id="image-upload-box">
                  <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #A8E6CF; margin-bottom: 15px;"></i>
                  <p style="margin: 0; color: #2c5f2d; font-weight: 600;">Cliquez pour ajouter une photo</p>
                  <p style="margin: 5px 0 0 0; color: #6c757d; font-size: 14px;">JPG, PNG ou GIF (max 5MB)</p>
                </div>
                <div class="image-preview" id="image-preview" style="display: none;">
                  <img id="preview-img" src="" alt="Aperçu">
                  <button type="button" class="remove-image" id="remove-image">
                    <i class="fas fa-times"></i>
                  </button>
                </div>
              </div>
              <span class="error-msg" id="image-don_error"></span>
            </div>
          </div>

          <!-- Association -->
          <div class="form-group full">
            <label for="association">Association bénéficiaire *</label>
            <select id="association" name="association_id">
              <option value="">Sélectionner une association</option>
              <?php
              if ($associations) {
                  foreach ($associations as $assoc) {
                      echo '<option value="' . $assoc['id'] . '">' . htmlspecialchars($assoc['nom']) . '</option>';
                  }
              }
              ?>
            </select>
            <span class="error-msg" id="association_error"></span>
          </div>

          <!-- Email -->
          <div class="form-group full">
            <label for="email">Adresse e-mail *</label>
            <input type="text" id="email" name="email" placeholder="exemple@email.com">
            <span class="error-msg" id="email_error"></span>
          </div>

          <button type="submit" class="submit-btn" id="submit-btn">
            <i class="fas fa-heart"></i> Valider mon don
          </button>
        </div>
      </form>
    </div>
  </div>

<?php
// Inclure le footer
include __DIR__ . '/includes/footer.php';
?>