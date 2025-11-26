<?php
// admin_propositions.php
?>
<main class="admin-main">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/back_style.css">
    <h2>Back-office : Propositions</h2>
    
    <div class="admin-actions">
        <a href="index.php?page=admin_propositions&add=1" class="btn-admin btn-add">+ Ajouter une proposition</a>
        <a href="index.php?page=admin_events" class="btn-admin">Événements</a>
        <a href="index.php?page=admin_inscriptions" class="btn-admin">Inscriptions</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Association</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Type</th>
                <th>Description</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($propositions as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= htmlspecialchars($p['association_nom']) ?></td>
                <td><?= htmlspecialchars($p['email_contact']) ?></td>
                <td><?= htmlspecialchars($p['tel']) ?></td>
                <td><?= htmlspecialchars($p['type']) ?></td>
                <td><?= htmlspecialchars(substr($p['description'], 0, 50)) ?>...</td>
                <td><?= $p['date_proposition'] ?></td>
                <td class="actions">
                    <a href="index.php?page=admin_propositions&edit=<?= $p['id'] ?>" class="btn-edit">Modifier</a>
                    <a href="index.php?page=admin_propositions&delete=<?= $p['id'] ?>" 
                       class="btn-delete" 
                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette proposition ?')">
                       Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</main>
