<?php
// admin_inscriptions_form.php
$isEdit = isset($inscription) && $inscription;
$pageTitle = $isEdit ? 'Modifier une inscription' : 'Ajouter une inscription';
?>
<main class="admin-main">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <h2><?= $pageTitle ?></h2>
    
    <div class="admin-actions">
        <a href="index.php?page=admin_inscriptions" class="btn-admin">← Retour à la liste</a>
    </div>

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label>Événement*</label>
                <select name="evenement_id" required>
                    <option value="">-- Sélectionner un événement --</option>
                    <?php foreach($events as $event): ?>
                        <option value="<?= $event['id'] ?>" <?= ($isEdit && $inscription['evenement_id'] == $event['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($event['titre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nom*</label>
                <input type="text" name="nom" value="<?= $isEdit ? htmlspecialchars($inscription['nom']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Prénom*</label>
                <input type="text" name="prenom" value="<?= $isEdit ? htmlspecialchars($inscription['prenom']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Âge*</label>
                <input type="number" name="age" min="12" max="70" value="<?= $isEdit ? $inscription['age'] : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Email*</label>
                <input type="email" name="email" value="<?= $isEdit ? htmlspecialchars($inscription['email']) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Téléphone*</label>
                <input type="text" name="tel" value="<?= $isEdit ? htmlspecialchars($inscription['tel']) : '' ?>" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $isEdit ? 'Mettre à jour' : 'Ajouter' ?></button>
                <a href="index.php?page=admin_inscriptions" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>
