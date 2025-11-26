<?php
// admin_propositions_form.php
$isEdit = isset($proposition) && $proposition;
$pageTitle = $isEdit ? 'Modifier une proposition' : 'Ajouter une proposition';
?>
<main class="admin-main">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <h2><?= $pageTitle ?></h2>
    
    <div class="admin-actions">
        <a href="index.php?page=admin_propositions" class="btn-admin">← Retour à la liste</a>
    </div>

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label>Nom de l'association*</label>
                <input type="text" name="association_nom" value="<?= $isEdit ? htmlspecialchars($proposition['association_nom']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Email de contact*</label>
                <input type="email" name="email_contact" value="<?= $isEdit ? htmlspecialchars($proposition['email_contact']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Téléphone*</label>
                <input type="text" name="tel" value="<?= $isEdit ? htmlspecialchars($proposition['tel']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Type d'événement*</label>
                <input type="text" name="type" value="<?= $isEdit ? htmlspecialchars($proposition['type']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Description*</label>
                <textarea name="description" rows="6" required><?= $isEdit ? htmlspecialchars($proposition['description']) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $isEdit ? 'Mettre à jour' : 'Ajouter' ?></button>
                <a href="index.php?page=admin_propositions" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>
