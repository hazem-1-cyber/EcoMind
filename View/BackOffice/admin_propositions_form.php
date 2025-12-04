<?php
// admin_propositions_form.php
$isEdit = isset($proposition) && $proposition;
$pageTitle = $isEdit ? 'Modifier une proposition' : 'Ajouter une proposition';
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
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#inscriptions" class="nav-item">
                <i class="fas fa-users"></i>
                <span>Inscriptions</span>
            </a>
            <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="nav-item active">
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
                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="btn-admin">← Retour aux propositions</a>
            </div>
        </div>

        <main class="admin-main">

    <div class="form-container">
        <form method="POST" class="admin-form">
            <div class="form-group">
                <label>Nom de l'association*</label>
                <input type="text" name="association_nom" value="<?= $isEdit ? htmlspecialchars($proposition->getAssociationNom()) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Email de contact*</label>
                <input type="email" name="email_contact" value="<?= $isEdit ? htmlspecialchars($proposition->getEmailContact()) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Téléphone*</label>
                <input type="text" name="tel" value="<?= $isEdit ? htmlspecialchars($proposition->getTel()) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Type d'événement*</label>
                <input type="text" name="type" value="<?= $isEdit ? htmlspecialchars($proposition->getType()) : '' ?>" required>
            </div>

            <div class="form-group">
                <label>Description*</label>
                <textarea name="description" rows="6" required><?= $isEdit ? htmlspecialchars($proposition->getDescription()) : '' ?></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><?= $isEdit ? 'Mettre à jour' : 'Ajouter' ?></button>
                <a href="<?= BASE_URL ?>/index.php?page=admin_dashboard#propositions" class="btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</main>

    </div>
</div>

</body>
</html>
