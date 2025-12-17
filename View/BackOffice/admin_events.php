<?php
// admin_events.php
?>
<main class="admin-main">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <h2>Back-office : Événements</h2>
    
    <div class="admin-actions">
        <a href="index.php?page=admin_events&add=1" class="btn-admin btn-add">+ Ajouter un événement</a>
        <a href="index.php?page=admin_inscriptions" class="btn-admin">Inscriptions</a>
        <a href="index.php?page=admin_propositions" class="btn-admin">Propositions</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Titre</th>
                <th>Type</th>
                <th>Date création</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($events as $e): ?>
            <tr>
                <td><?= $e['id'] ?></td>
                <td><?= htmlspecialchars($e['titre']) ?></td>
                <td><?= htmlspecialchars($e['type']) ?></td>
                <td><?= $e['date_creation'] ?></td>
                <td class="actions">
                    <a href="index.php?page=admin_events&edit=<?= $e['id'] ?>" class="btn-edit">Modifier</a>
                    <a href="index.php?page=admin_events&delete=<?= $e['id'] ?>" 
                       class="btn-delete" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                       Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
