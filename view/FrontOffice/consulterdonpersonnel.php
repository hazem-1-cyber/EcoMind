<?php
// Configuration pour le header
$pageTitle = "EcoMind - Mes dons";
$additionalCSS = ['style.css', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'];

// Inclure le header
include __DIR__ . '/includes/header.php';

require_once __DIR__ . "/../../controller/DonController.php";

$dons = [];
$email = '';
$showResults = false;
$errorMessage = '';

// Traiter la recherche quand le formulaire est soumis
if (isset($_GET['search']) && isset($_GET['email']) && !empty($_GET['email'])) {
    $email = trim($_GET['email']);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        try {
            $donCtrl = new DonController();
            $dons = $donCtrl->getDonsByEmail($email);
            $showResults = true;
            
            // Log pour debug
            error_log("Recherche pour email: " . $email);
            error_log("Nombre de dons trouvés: " . count($dons));
            
        } catch (Exception $e) {
            $errorMessage = "Une erreur est survenue lors de la recherche.";
            error_log("Erreur consultation dons: " . $e->getMessage());
        }
    } else {
        $errorMessage = "Adresse email invalide.";
    }
}
?>

  <div class="don-container">
    <div class="form-card">
      <h1>Rechercher mes dons</h1>
      <p class="subtitle">Consultez l'historique de vos dons en saisissant votre adresse e-mail.</p>
      
      <?php if ($errorMessage): ?>
        <div class="error-box" style="background: #fadbd8; color: #c0392b; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center;">
          ✗ <?= htmlspecialchars($errorMessage) ?>
        </div>
      <?php endif; ?>
      
      <form id="consult-form" method="GET" action="" novalidate>
        <div class="form-grid">
          <div class="form-group full">
          <label for="email">Votre adresse e-mail *</label>
          <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" placeholder="exemple@email.com" required>
          <span class="error-msg" id="email_error"></span>
                  </div>
        </div>
        
        <input type="hidden" name="search" value="1">

        <button type="submit" class="submit-btn">Rechercher mes dons</button>
        <a href="addDon.php" class="submit-btn" style="display: inline-block; text-align: center; text-decoration: none;">Faire un nouveau don</a>
      </form>
    </div>
  </div>

      <?php if ($showResults): ?>
        <div class="results-section">
          <h2>Mes dons (<?= count($dons) ?>)</h2>
          
          <!-- Debug info (à supprimer en production) -->
          <?php if (isset($_GET['debug'])): ?>
            <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; border-radius: 5px; font-size: 12px;">
              <strong>Debug Info:</strong><br>
              Email recherché: <?= htmlspecialchars($email) ?><br>
              Nombre de résultats: <?= count($dons) ?><br>
              <?php if (count($dons) > 0): ?>
                Premier don ID: <?= $dons[0]['id'] ?? 'N/A' ?><br>
                Type: <?= $dons[0]['type_don'] ?? 'N/A' ?><br>
              <?php endif; ?>
            </div>
          <?php endif; ?>
          
          <?php if (empty($dons)): ?>
            <div class="no-results-card">
              <div class="no-results-icon">
                <i class="fas fa-inbox"></i>
              </div>
              <h3>Aucun don trouvé</h3>
              <p>Vous n'avez pas encore effectué de don avec cette adresse email.</p>
              <a href="addDon.php" class="btn-link-primary">Faire mon premier don</a>
            </div>
          <?php else: ?>
            <div class="dons-list">
              <?php foreach ($dons as $don): ?>
                <div class="don-card">
                  <div class="don-header">
                    <span class="don-id">Don #<?= $don['id'] ?></span>
                    <span class="don-date"><?= date('d/m/Y', strtotime($don['created_at'])) ?></span>
                  </div>
                  
                  <div class="don-details">
                    <p><strong>Type:</strong> <?= ucfirst(str_replace('_', ' ', $don['type_don'])) ?></p>
                    
                    <?php if ($don['type_don'] === 'money' && $don['montant']): ?>
                      <p><strong>Montant:</strong> <?= number_format($don['montant'], 2) ?> TND</p>
                    <?php endif; ?>
                    
                    <?php if ($don['ville']): ?>
                      <p><strong>Ville:</strong> <?= htmlspecialchars($don['ville']) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($don['livraison']): ?>
                      <p><strong>Livraison:</strong> <?= ucfirst(str_replace('_', ' ', $don['livraison'])) ?></p>
                    <?php endif; ?>
                    
                    <p><strong>Statut:</strong> 
                      <span class="statut-badge statut-<?= $don['statut'] ?>">
                        <?php
                        switch($don['statut']) {
                          case 'pending': echo 'En attente'; break;
                          case 'validated': echo 'Validé'; break;
                          case 'rejected': echo 'Rejeté'; break;
                          case 'cancelled': echo 'Annulé'; break;
                          default: echo $don['statut'];
                        }
                        ?>
                      </span>
                    </p>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <script>
    // Validation JavaScript sans HTML5
    const form = document.getElementById("consult-form");
    
    function setMsg(id, text, isValid) {
        const msg = document.getElementById(id);
        if (msg) {
            msg.innerText = text;
            msg.style.color = isValid ? "green" : "red";
            msg.style.fontSize = "0.85em";
        }
    }

    // Validation Email
    const emailInput = document.getElementById("email");
    if (emailInput) {
        emailInput.addEventListener("blur", function () {
            const value = this.value.trim();
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (value && regex.test(value)) {
                setMsg("email_error", "✓ Email valide", true);
            } else if (value) {
                setMsg("email_error", "✗ Email invalide", false);
            }
        });
    }

    // Validation finale
    if (form) {
        form.addEventListener("submit", function (event) {
            const email = document.getElementById("email").value.trim();
            
            if (!email) {
                event.preventDefault();
                setMsg("email_error", "✗ Email requis", false);
                return false;
            }
            
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                event.preventDefault();
                setMsg("email_error", "✗ Email invalide", false);
                return false;
            }
            
            return true;
        });
    }
  </script>

<?php
// Inclure le footer
include __DIR__ . '/includes/footer.php';
?>