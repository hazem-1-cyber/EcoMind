<?php
// admin_inscriptions_form.php
$isEdit = isset($inscription) && $inscription;
$pageTitle = $isEdit ? 'Modifier une inscription' : 'Ajouter une inscription';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EcoMind - <?= $pageTitle ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
</head>
<body>

<div class="dashboard-container">
    <!-- SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon">🌱</div>
                <div class="logo-text">EcoMind</div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard" class="nav-item">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#events" class="nav-item">
                <i class="fas fa-calendar-alt"></i>
                <span>Événements</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="nav-item active">
                <i class="fas fa-users"></i>
                <span>Inscriptions</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="nav-item">
                <i class="fas fa-lightbulb"></i>
                <span>Propositions</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=events" class="nav-item">
                <i class="fas fa-arrow-left"></i>
                <span>Retour au site</span>
            </a>
        </nav>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <div class="top-header">
            <div class="header-left">
                <h1><?= $pageTitle ?></h1>
            </div>
            <div class="header-right">
                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="btn-admin">← Retour aux inscriptions</a>
            </div>
        </div>

        <main class="admin-main">

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
                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>

    </div>
</div>

</body>
</html>
