<?php
// event_detail.php
?>
<main class="event-detail">

    <div class="detail-top">
        <img src="<?= htmlspecialchars($event->getImageMain()) ?>" 
             alt="<?= htmlspecialchars($event->getTitre()) ?>" 
             class="detail-main-img">
    </div>

    <h2 class="detail-title"><?= htmlspecialchars($event->getTitre()) ?></h2>

    <section class="description">
        <h3>Description</h3>
        <p class="detail-text"><?= nl2br(htmlspecialchars($event->getDescription())) ?></p>

        <div class="secondary-image">
            <img src="<?= htmlspecialchars($event->getImageSecond()) ?>" 
                 alt="Secondaire" 
                 class="detail-second-img">
        </div>

        <div class="cta-inscription">
            <a class="btn-inscrire" href="index.php?page=inscription&id=<?= $event->getId() ?>">
                S'inscrire
            </a>
        </div>
    </section>

</main>
