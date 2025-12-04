<?php
// events.php
?>
<main class="events-main">
    <div class="events-grid">
        <?php foreach ($events as $ev): ?>
            <div class="event-card">
                <a href="index.php?page=event_detail&id=<?= $ev->getId() ?>">
                    <img src="<?= htmlspecialchars($ev->getImageMain()) ?>" 
                         alt="<?= htmlspecialchars($ev->getTitre()) ?>" 
                         class="event-thumb">
                </a>

                <h3 class="event-title"><?= htmlspecialchars($ev->getTitre()) ?></h3>

                <a class="btn-savoir-plus" 
                   href="index.php?page=event_detail&id=<?= $ev->getId() ?>">
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
