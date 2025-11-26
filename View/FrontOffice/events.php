<?php
// events.php
?>
<main class="events-main">
    <div class="events-grid">
        <?php foreach ($events as $ev): ?>
            <div class="event-card">
                <a href="index.php?page=events&id=<?= $ev['id'] ?>">
                    <img src="<?= htmlspecialchars($ev['image_main']) ?>" 
                         alt="<?= htmlspecialchars($ev['titre']) ?>" 
                         class="event-thumb">
                </a>

                <h3 class="event-title"><?= htmlspecialchars($ev['titre']) ?></h3>

                <a class="btn-savoir-plus" 
                   href="index.php?page=events&id=<?= $ev['id'] ?>">
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
