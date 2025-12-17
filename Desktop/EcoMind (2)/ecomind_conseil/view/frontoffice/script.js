let currentProgress = 0;

function updateProgress(percentage, buttonElement) {
    console.log('updateProgress appelé avec:', percentage, 'Progress actuel:', currentProgress);
    
    if (currentProgress >= 100) {
        console.log('Progress déjà à 100%, arrêt');
        return;
    }
    
    currentProgress += percentage;
    
    if (currentProgress > 100) {
        currentProgress = 100;
    }
    
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const progressInfo = document.getElementById('progressInfo');
    
    progressBar.style.width = currentProgress + '%';
    progressText.textContent = Math.round(currentProgress) + '%';
    
    // Mettre à jour le message de progression
    if (progressInfo) {
        if (currentProgress === 33) {
            progressInfo.textContent = '🌱 Excellent ! 1 conseil appliqué sur 3. Continuez !';
            progressInfo.style.color = '#4CAF50';
        } else if (currentProgress === 66) {
            progressInfo.textContent = '🌟 Fantastique ! 2 conseils appliqués sur 3. Encore un !';
            progressInfo.style.color = '#FF9800';
        } else if (currentProgress >= 100) {
            progressInfo.innerHTML = '🎉 <strong>INCROYABLE ! Vous avez appliqué tous les conseils !</strong> 🎉';
            progressInfo.style.color = '#d4af37';
            progressInfo.style.fontWeight = 'bold';
            progressInfo.style.fontSize = '1.2em';
        }
    }
    
    // Désactiver le bouton cliqué
    if (buttonElement) {
        buttonElement.textContent = '✅ Appliqué !';
        buttonElement.style.background = '#4CAF50';
        buttonElement.style.cursor = 'default';
        buttonElement.onclick = null;
    }
    
    console.log('Progress mis à jour à:', currentProgress);
    
    // Effet de célébration à 100%
    if (currentProgress >= 100) {
        console.log('Déclenchement de la célébration !');
        setTimeout(() => {
            showBravoEffect();
        }, 500); // Petit délai pour voir la barre se remplir
    }
}

function showBravoEffect() {
    console.log('🎉 showBravoEffect appelé !');
    
    // Créer l'effet BRAVO géant
    const bravoEffect = document.createElement('div');
    bravoEffect.className = 'bravo-effect';
    bravoEffect.innerHTML = `
        <div class="bravo-text">BRAVO!</div>
        <div class="bravo-subtext">🌟 INCROYABLE ! 🌟</div>
    `;
    
    document.body.appendChild(bravoEffect);
    console.log('BRAVO effect ajouté au DOM');
    
    // Lancer les confettis immédiatement
    createConfetti();
    
    // Animation d'apparition du BRAVO
    setTimeout(() => {
        bravoEffect.classList.add('show');
        console.log('BRAVO effect show ajouté');
    }, 100);
    
    // Faire disparaître le BRAVO et lancer la célébration complète
    setTimeout(() => {
        bravoEffect.classList.add('hide');
        setTimeout(() => {
            if (bravoEffect.parentNode) {
                bravoEffect.parentNode.removeChild(bravoEffect);
            }
            celebrateSuccess();
        }, 800);
    }, 2500);
}

function celebrateSuccess() {
    // Créer l'overlay de célébration
    const celebrationOverlay = document.createElement('div');
    celebrationOverlay.className = 'celebration-overlay';
    celebrationOverlay.innerHTML = `
        <div class="celebration-content">
            <div class="celebration-icon">🎉</div>
            <h1 class="celebration-title">FÉLICITATIONS !</h1>
            <p class="celebration-message">Vous avez appliqué tous les conseils écologiques !</p>
            <p class="celebration-submessage">Vous êtes maintenant un véritable éco-héros ! 🌱</p>
            <div class="celebration-stats">
                <div class="stat-item">
                    <span class="stat-number">100%</span>
                    <span class="stat-label">Conseils appliqués</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">🌍</span>
                    <span class="stat-label">Impact positif</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">⭐</span>
                    <span class="stat-label">Éco-héros</span>
                </div>
            </div>
            <button class="celebration-close" onclick="closeCelebration()">Continuer</button>
        </div>
    `;
    
    document.body.appendChild(celebrationOverlay);
    
    // Lancer les confettis
    createConfetti();
    
    // Animation d'apparition
    setTimeout(() => {
        celebrationOverlay.classList.add('show');
    }, 100);
    
    // Faire vibrer la barre de progression
    const progressBar = document.getElementById('progressBar');
    progressBar.classList.add('celebration-pulse');
    
    // Changer le texte de progression
    const progressInfo = document.querySelector('.progress-info');
    if (progressInfo) {
        progressInfo.innerHTML = '🎉 <strong>BRAVO ! Vous avez appliqué tous les conseils !</strong> 🎉';
        progressInfo.style.color = '#d4af37';
        progressInfo.style.fontWeight = 'bold';
        progressInfo.style.fontSize = '1.2em';
    }
    
    // Désactiver tous les boutons
    const buttons = document.querySelectorAll('.tip-button');
    buttons.forEach(button => {
        button.textContent = '✅ Appliqué !';
        button.style.background = '#4CAF50';
        button.style.cursor = 'default';
        button.onclick = null;
    });
}

function createConfetti() {
    const colors = ['#FFD700', '#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#98D8C8', '#F39C12', '#E74C3C', '#9B59B6', '#1ABC9C'];
    const shapes = ['circle', 'square', 'triangle'];
    const confettiCount = 200;
    
    for (let i = 0; i < confettiCount; i++) {
        setTimeout(() => {
            const confetti = document.createElement('div');
            const shape = shapes[Math.floor(Math.random() * shapes.length)];
            
            confetti.className = `confetti confetti-${shape}`;
            confetti.style.left = Math.random() * 100 + 'vw';
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.animationDelay = Math.random() * 2 + 's';
            confetti.style.animationDuration = (Math.random() * 4 + 3) + 's';
            
            // Taille aléatoire
            const size = Math.random() * 8 + 6;
            confetti.style.width = size + 'px';
            confetti.style.height = size + 'px';
            
            document.body.appendChild(confetti);
            
            // Supprimer le confetti après l'animation
            setTimeout(() => {
                if (confetti.parentNode) {
                    confetti.parentNode.removeChild(confetti);
                }
            }, 8000);
        }, i * 8);
    }
    
    // Ajouter des confettis spéciaux dorés
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const goldConfetti = document.createElement('div');
            goldConfetti.className = 'confetti confetti-gold';
            goldConfetti.style.left = Math.random() * 100 + 'vw';
            goldConfetti.style.animationDelay = Math.random() * 2 + 's';
            goldConfetti.style.animationDuration = (Math.random() * 3 + 4) + 's';
            document.body.appendChild(goldConfetti);
            
            setTimeout(() => {
                if (goldConfetti.parentNode) {
                    goldConfetti.parentNode.removeChild(goldConfetti);
                }
            }, 8000);
        }, i * 15);
    }
}

function closeCelebration() {
    const overlay = document.querySelector('.celebration-overlay');
    if (overlay) {
        overlay.classList.add('hide');
        setTimeout(() => {
            if (overlay.parentNode) {
                overlay.parentNode.removeChild(overlay);
            }
        }, 500);
    }
    
    // Arrêter l'animation de la barre de progression
    const progressBar = document.getElementById('progressBar');
    progressBar.classList.remove('celebration-pulse');
}

document.addEventListener('DOMContentLoaded', () => {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    progressBar.style.width = '0%';
    progressText.textContent = '0%';
});