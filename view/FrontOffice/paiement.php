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