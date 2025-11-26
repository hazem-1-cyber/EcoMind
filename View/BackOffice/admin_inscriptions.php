<?php
// admin_inscriptions.php
?>
<main class="admin-main">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <h2>Back-office : Inscriptions</h2>
    
    <div class="admin-actions">
        <a href="index.php?page=admin_inscriptions&add=1" class="btn-admin btn-add">+ Ajouter une inscription</a>
        <a href="index.php?page=admin_events" class="btn-admin">Événements</a>
        <a href="index.php?page=admin_propositions" class="btn-admin">Propositions</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Événement ID</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Âge</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($inscriptions as $ins): ?>
            <tr>
                <td><?= $ins['id'] ?></td>
                <td><?= $ins['evenement_id'] ?></td>
                <td><?= htmlspecialchars($ins['nom']) ?></td>
                <td><?= htmlspecialchars($ins['prenom']) ?></td>
                <td><?= $ins['age'] ?></td>
                <td><?= htmlspecialchars($ins['email']) ?></td>
                <td><?= htmlspecialchars($ins['tel']) ?></td>
                <td><?= $ins['date_inscription'] ?></td>
                <td class="actions">
                    <a href="index.php?page=admin_inscriptions&edit=<?= $ins['id'] ?>" class="btn-edit">Modifier</a>
                    <a href="index.php?page=admin_inscriptions&delete=<?= $ins['id'] ?>" 
                       class="btn-delete" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette inscription ?')">
                       Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
