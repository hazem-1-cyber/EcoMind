<?php
// Configuration pour le header
$pageTitle = "EcoMind - Merci pour votre don";
$additionalCSS = ['style.css'];

// Inclure le header
include __DIR__ . '/includes/header.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
        body {
            background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 50%, #a5d6a7 100%);
            min-height: 100vh;
        }

        .merci-page {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            position: relative;
        }

        /* Animation des feuilles et plantes qui tombent */
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
            font-size: 20px;
            animation: fall linear infinite;
            opacity: 0.6;
        }

        @keyframes fall {
            0% {
                transform: translateY(-50px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.6;
            }
            90% {
                opacity: 0.4;
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
                opacity: 0;
            }
        }

        .thank-you-container {
            position: relative;
            z-index: 10;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(15px);
            padding: 40px 30px;
            border-radius: 30px;
            box-shadow: 0 25px 80px rgba(44, 95, 45, 0.3);
            text-align: center;
            max-width: 700px;
            width: 100%;
            animation: scaleIn 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            border: 3px solid rgba(168, 230, 207, 0.5);
        }
        
        @media (max-width: 768px) {
            .thank-you-container {
                padding: 30px 20px;
            }
        }

        @keyframes scaleIn {
            from {
                opacity: 0;
                transform: scale(0.5) rotate(-10deg);
            }
            to {
                opacity: 1;
                transform: scale(1) rotate(0deg);
            }
        }

        .success-icon {
            font-size: 80px;
            color: #28a745;
            margin-bottom: 20px;
            animation: bounce 1.5s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            .success-icon {
                font-size: 60px;
            }
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0) scale(1);
            }
            50% {
                transform: translateY(-20px) scale(1.1);
            }
        }

        h1 {
            font-size: 36px;
            background: linear-gradient(135deg, #2c5f2d, #88b04b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            animation: shimmer 2s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            h1 {
                font-size: 28px;
            }
        }

        @keyframes shimmer {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        .message {
            font-size: 18px;
            color: #2c5f2d;
            margin-bottom: 20px;
            line-height: 1.6;
        }
        
        @media (max-width: 768px) {
            .message {
                font-size: 16px;
            }
        }

        .highlight-box {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            padding: 20px;
            border-radius: 15px;
            margin: 20px 0;
            border-left: 5px solid #28a745;
            animation: slideInLeft 1s ease-out;
        }
        
        @media (max-width: 768px) {
            .highlight-box {
                padding: 15px;
            }
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .highlight-box p {
            color: #155724;
            font-size: 16px;
            margin: 10px 0;
        }

        .buttons-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .buttons-container {
                flex-direction: column;
                gap: 10px;
            }
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: linear-gradient(135deg, #2c5f2d, #88b04b);
            color: white;
            padding: 15px 35px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            box-shadow: 0 10px 30px rgba(44, 95, 45, 0.3);
            position: relative;
            overflow: hidden;
            z-index: 1;
        }
        
        @media (max-width: 768px) {
            .btn-action {
                width: 100%;
                padding: 12px 25px;
                font-size: 15px;
            }
        }

        .btn-action.btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            box-shadow: 0 10px 30px rgba(108, 117, 125, 0.3);
        }

        .btn-action::before {
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

        .btn-action:hover::before {
            width: 400px;
            height: 400px;
        }

        .btn-action:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 40px rgba(44, 95, 45, 0.5);
        }

        .btn-action.btn-secondary:hover {
            box-shadow: 0 15px 40px rgba(108, 117, 125, 0.5);
        }

        .eco-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin: 20px 0;
            font-size: 35px;
            animation: float 3s ease-in-out infinite;
        }
        
        @media (max-width: 768px) {
            .eco-icons {
                font-size: 28px;
                gap: 15px;
            }
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        .eco-icons span {
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    </style>

<!-- Animation des feuilles qui tombent -->
<div class="falling-leaves" id="fallingLeaves"></div>

<div class="merci-page">
    <div class="thank-you-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>

        <h1>🌱 Merci pour votre générosité ! 🌱</h1>

        <p class="message">
            Votre don a été enregistré avec succès. Grâce à vous, nous pouvons continuer notre mission 
            de protection de l'environnement en Tunisie.
        </p>

        <div class="eco-icons">
            <span>🌍</span>
            <span>🌿</span>
            <span>♻️</span>
            <span>🌳</span>
        </div>

        <div class="highlight-box">
            <p><strong>📧 Confirmation envoyée</strong></p>
            <p>Un email de confirmation vous a été envoyé avec tous les détails de votre don.</p>
            <p><strong>⏳ Traitement en cours</strong></p>
            <p>Notre équipe va examiner votre don dans les plus brefs délais.</p>
        </div>

        <p class="message" style="font-size: 16px; color: #6c757d;">
            Ensemble, construisons un avenir plus vert et durable ! 💚
        </p>

        <div class="buttons-container">
            <a href="index.php" class="btn-action btn-secondary">
                <i class="fas fa-home"></i> Retour à l'accueil
            </a>
            <a href="addDon.php" class="btn-action">
                <i class="fas fa-heart"></i> Faire un autre don
            </a>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>

<script>
        // Animation des feuilles et plantes qui tombent
        const leavesContainer = document.getElementById('fallingLeaves');
        const leafEmojis = ['🌿', '🍃'];
        const numberOfLeaves = 15; // Réduit de 35 à 15

        function createLeaf() {
            const leaf = document.createElement('div');
            leaf.className = 'leaf';
            leaf.textContent = leafEmojis[Math.floor(Math.random() * leafEmojis.length)];
            
            // Position aléatoire horizontale
            leaf.style.left = Math.random() * 100 + '%';
            
            // Durée d'animation aléatoire (entre 8 et 15 secondes) - plus lent
            const duration = Math.random() * 7 + 8;
            leaf.style.animationDuration = duration + 's';
            
            // Délai aléatoire avant le début (entre 0 et 5 secondes)
            const delay = Math.random() * 5;
            leaf.style.animationDelay = delay + 's';
            
            // Taille aléatoire plus petite (entre 15 et 25px)
            const size = Math.random() * 10 + 15;
            leaf.style.fontSize = size + 'px';
            
            leavesContainer.appendChild(leaf);
            
            // Supprimer la feuille après l'animation
            setTimeout(() => {
                leaf.remove();
                createLeaf(); // Créer une nouvelle feuille
            }, (duration + delay) * 1000);
        }

        // Créer les feuilles initiales avec des délais progressifs
        for (let i = 0; i < numberOfLeaves; i++) {
            setTimeout(() => createLeaf(), i * 500); // Délai de 500ms entre chaque feuille
        }
    </script>
