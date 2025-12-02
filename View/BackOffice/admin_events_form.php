<?php
// admin_events_form.php
$isEdit = isset($event) && $event;
$pageTitle = $isEdit ? 'Modifier un événement' : 'Ajouter un événement';
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
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#events" class="nav-item active">
                <i class="fas fa-calendar-alt"></i>
                <span>Événements</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="nav-item">
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
                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#events" class="btn-admin">← Retour aux événements</a>
            </div>
        </div>

        <main class="admin-main">

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
                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#events" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>

    </div>
</div>

</body>
</html>
