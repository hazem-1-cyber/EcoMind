<?php
// events.php
?>
<main class="events-main">
    <?php if (isset($_GET['msg']) && $_GET['msg'] === 'inscription_ok'): ?>
        <div style="background: linear-gradient(135deg, #A8E6CF, #90E0B8); color: #013220; padding: 20px; margin: 20px 0; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(168, 230, 207, 0.3);">
            <h3 style="margin: 0 0 10px 0;">🎉 Inscription confirmée !</h3>
            <p style="margin: 0;">Merci de votre inscription ! Un email de bienvenue vous a été envoyé avec tous les détails. Vous serez notifié(e) quand l'événement approchera.</p>
        </div>
    <?php endif; ?>
    <div class="events-grid">
        <?php foreach ($events as $ev): ?>
            <div class="event-card">
                <a href="index.php?page=events&id=<?= $ev->getId() ?>">
                    <img src="<?= htmlspecialchars($ev->getImageMain()) ?>" 
                         alt="<?= htmlspecialchars($ev->getTitre()) ?>" 
                         class="event-thumb">
                </a>

                <h3 class="event-title"><?= htmlspecialchars($ev->getTitre()) ?></h3>

                <a class="btn-savoir-plus" 
                   href="index.php?page=events&id=<?= $ev->getId() ?>">
                   Savoir plus
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="proposer-cta">
        <a href="index.php?page=proposer" class="btn-proposer">
            Proposer un événement
        </a>
    </div>
</main>
