<?php
session_start();
require_once __DIR__ . '/../../config.php';

// Vérifier que les données du don sont en session
if (!isset($_SESSION['don_data'])) {
    header('Location: index.php');
    exit;
}

$donData = $_SESSION['don_data'];
$montant = $donData['montant'] ?? 0;
$email = $donData['email'] ?? '';

// Configuration pour le header
$pageTitle = "EcoMind - Paiement Sécurisé";
$additionalCSS = ['style.css'];

// Inclure le header
include __DIR__ . '/includes/header.php';
?>

<script src="https://js.stripe.com/v3/"></script>

<!-- Animation de feuilles qui tombent -->
<div class="falling-leaves" id="fallingLeaves"></div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const leavesContainer = document.getElementById('fallingLeaves');
  const leafEmojis = ['🌿', '🍃', '💚'];
  const numberOfLeaves = 20;

  function createLeaf() {
    const leaf = document.createElement('div');
    leaf.className = 'leaf';
    leaf.textContent = leafEmojis[Math.floor(Math.random() * leafEmojis.length)];
    
    leaf.style.left = Math.random() * 100 + '%';
    const duration = Math.random() * 7 + 8;
    leaf.style.animationDuration = duration + 's';
    const delay = Math.random() * 5;
    leaf.style.animationDelay = delay + 's';
    const size = Math.random() * 20 + 20;
    leaf.style.fontSize = size + 'px';
    
    leavesContainer.appendChild(leaf);
    
    setTimeout(() => {
      leaf.remove();
      createLeaf();
    }, (duration + delay) * 1000);
  }

  for (let i = 0; i < numberOfLeaves; i++) {
    setTimeout(() => createLeaf(), i * 300);
  }
});
</script>

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
    opacity: 0.8;
  }
  100% {
    transform: translateY(100vh) rotate(360deg);
    opacity: 0;
  }
}

/* Animation de la carte principale */
.form-card {
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

/* Animation du récapitulatif */
.payment-summary {
  animation: fadeInUp 0.8s ease-out;
  transition: transform 0.3s, box-shadow 0.3s;
}

.payment-summary:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(44, 95, 45, 0.15);
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Animation du bouton de paiement */
.payment-btn {
  transition: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
  position: relative;
  overflow: hidden;
}

.payment-btn::before {
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

.payment-btn:hover::before {
  width: 300px;
  height: 300px;
}

.payment-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(44, 95, 45, 0.3);
}

/* Animation de l'icône de sécurité */
.security-info {
  animation: fadeIn 1s ease-out;
}

.security-info p {
  animation: pulse 2s ease-in-out infinite;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes pulse {
  0%, 100% { opacity: 1; }
  50% { opacity: 0.7; }
}

/* Animation du champ de carte */
#card-element {
  transition: all 0.3s;
}

#card-element:focus-within {
  border-color: #2c5f2d;
  box-shadow: 0 0 0 3px rgba(44, 95, 45, 0.1);
}
</style>

<div class="don-container">
  <div class="form-card">
    <h1>💳 Paiement en Ligne</h1>
    <p class="subtitle">Finalisez votre don par carte bancaire avec Stripe</p>

    <!-- Récapitulatif du don -->
    <div class="payment-summary">
      <h3>Récapitulatif</h3>
      <div class="summary-item">
        <span>Montant du don:</span>
        <strong><?= number_format($montant, 2) ?> TND</strong>
      </div>
      <div class="summary-item">
        <span>Montant en USD:</span>
        <strong>~<?= number_format($montant / 3.1, 2) ?> USD</strong>
      </div>
      <div class="summary-item">
        <span>Email:</span>
        <strong><?= htmlspecialchars($email) ?></strong>
      </div>
    </div>
    


    <form id="payment-form">
      <div class="form-grid">
        <!-- Stripe Card Element -->
        <div class="form-group full">
          <label>Informations de carte *</label>
          <div id="card-element" style="padding: 12px; border: 1px solid #ddd; border-radius: 4px; background: white;"></div>
          <div id="card-errors" role="alert" style="color: #c0392b; margin-top: 8px; font-size: 14px;"></div>
        </div>

        <!-- Acceptation des conditions -->
        <div class="form-group full checkbox-group">
          <label>
            <input type="checkbox" id="accept-terms" required>
            J'accepte les <a href="#" target="_blank">conditions générales</a> et confirme le paiement
          </label>
          <span class="error-msg" id="accept-terms_error"></span>
        </div>

        <button type="submit" id="submit-button" class="submit-btn payment-btn">
          💳 Payer
        </button>
        
        <a href="addDon.php" class="btn-link">← Retour au formulaire</a>
      </div>
    </form>

    <?php
    // Afficher les erreurs de paiement
    if (isset($_SESSION['payment_errors']) && is_array($_SESSION['payment_errors']) && count($_SESSION['payment_errors']) > 0) {
      echo '<div class="alert alert-error" style="margin-top:16px;">';
      echo '<h4 style="color:#c0392b;">Erreurs de paiement :</h4><ul>';
      foreach ($_SESSION['payment_errors'] as $err) {
        echo '<li style="color:#c0392b;">' . htmlspecialchars($err) . '</li>';
      }
      echo '</ul></div>';
      unset($_SESSION['payment_errors']);
    }
    ?>

    <!-- Information -->
    <div class="security-info">
      <p>🔒 Paiement sécurisé par Stripe</p>
    </div>
  </div>
</div>

<script>
// Initialiser Stripe (remplacez par votre clé publique)
const stripe = Stripe('<?= STRIPE_PUBLIC_KEY ?>');
const elements = stripe.elements();

// Créer l'élément de carte
const cardElement = elements.create('card', {
  style: {
    base: {
      fontSize: '16px',
      color: '#32325d',
      fontFamily: '-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif',
      '::placeholder': {
        color: '#aab7c4'
      }
    },
    invalid: {
      color: '#c0392b'
    }
  }
});

cardElement.mount('#card-element');

// Gérer les erreurs de validation en temps réel
cardElement.on('change', function(event) {
  const displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.textContent = event.error.message;
  } else {
    displayError.textContent = '';
  }
});

// Gérer la soumission du formulaire
const form = document.getElementById('payment-form');
const submitButton = document.getElementById('submit-button');

form.addEventListener('submit', async function(event) {
  event.preventDefault();

  // Vérifier les conditions
  const acceptTerms = document.getElementById('accept-terms');
  if (!acceptTerms.checked) {
    document.getElementById('accept-terms_error').textContent = 'Vous devez accepter les conditions';
    return;
  }

  // Désactiver le bouton pour éviter les doubles soumissions
  submitButton.disabled = true;
  submitButton.textContent = 'Traitement en cours...';

  // Créer le Payment Intent
  const response = await fetch('create_payment_intent.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    }
  });

  const data = await response.json();

  if (data.error) {
    document.getElementById('card-errors').textContent = data.error;
    submitButton.disabled = false;
    submitButton.textContent = '💳 Payer <?= number_format($montant, 2) ?> TND';
    return;
  }

  // Confirmer le paiement
  const {error, paymentIntent} = await stripe.confirmCardPayment(data.clientSecret, {
    payment_method: {
      card: cardElement,
      billing_details: {
        email: '<?= htmlspecialchars($email) ?>'
      }
    }
  });

  if (error) {
    document.getElementById('card-errors').textContent = error.message;
    submitButton.disabled = false;
    submitButton.textContent = '💳 Payer';
  } else if (paymentIntent.status === 'succeeded') {
    // Rediriger vers le traitement du paiement
    window.location.href = 'process_payment.php?payment_intent=' + paymentIntent.id;
  }
});
</script>

<?php
include __DIR__ . '/includes/footer.php';
?>