<?php
// event_detail.php
?>
<main class="event-detail">

    <div class="detail-top">
        <img src="<?= htmlspecialchars($event['image_main']) ?>" 
             alt="<?= htmlspecialchars($event['titre']) ?>" 
             class="detail-main-img">
    </div>

    <h2 class="detail-title"><?= htmlspecialchars($event['titre']) ?></h2>

    <section class="description">
        <h3>Description</h3>
        <p class="detail-text"><?= nl2br(htmlspecialchars($event['description'])) ?></p>

        <div class="secondary-image">
            <img src="<?= htmlspecialchars($event['image_second']) ?>" 
                 alt="Secondaire" 
                 class="detail-second-img">
        </div>

        <div class="cta-inscription">
            <a class="btn-inscrire" href="index.php?page=inscription&id=<?= $event['id'] ?>">
                S'inscrire
            </a>
        </div>
    </section>

</main>
