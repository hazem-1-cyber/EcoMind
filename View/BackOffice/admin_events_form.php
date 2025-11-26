<?php
// admin_events_form.php
$isEdit = isset($event) && $event;
$pageTitle = $isEdit ? 'Modifier un événement' : 'Ajouter un événement';
?>
<main class="admin-main">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <h2><?= $pageTitle ?></h2>
    
    <div class="admin-actions">
        <a href="index.php?page=admin_events" class="btn-admin">← Retour à la liste</a>
    </div>

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label>Titre*</label>
                <input type="text" name="titre" value="<?= $isEdit ? htmlspecialchars($event['titre']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Type*</label>
                <input type="text" name="type" value="<?= $isEdit ? htmlspecialchars($event['type']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Description*</label>
                <textarea name="description" rows="6" required><?= $isEdit ? htmlspecialchars($event['description']) : '' ?></textarea>
            </div>

            <div class="form-group">
                <label>Image principale (chemin)*</label>
                <input type="text" name="image_main" value="<?= $isEdit ? htmlspecialchars($event['image_main']) : '' ?>" required>
                <small>Exemple: <?= BASE_URL ?>/uploads/events/image.png</small>
            </div>

            <div class="form-group">
                <label>Image secondaire (chemin)*</label>
                <input type="text" name="image_second" value="<?= $isEdit ? htmlspecialchars($event['image_second']) : '' ?>" required>
                <small>Exemple: <?= BASE_URL ?>/uploads/events/image2.png</small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $isEdit ? 'Mettre à jour' : 'Ajouter' ?></button>
                <a href="index.php?page=admin_events" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>
