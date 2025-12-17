<?php
// inscription.php
// $event est fourni par controller
?>
<div class="container">
    <header>
        <h1>S'inscrire à l'événement</h1>
        <p class="subtitle"><?= htmlspecialchars($event->getTitre()) ?></p>
    </header>

    <div class="tip-card">
        <div class="tip-icon"></div>
        <h3>Formulaire d'inscription</h3>
        <p>Remplissez les informations ci-dessous pour vous inscrire à cet événement.</p>
        
        <form id="inscription-form" action="index.php?page=inscription&id=<?= $event->getId() ?>" method="post" onsubmit="return confirmInscription();">
            <input type="hidden" name="evenement_id" value="<?= $event->getId() ?>">
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Nom*</label>
                <input type="text" name="nom" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Prénom*</label>
                <input type="text" name="prenom" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Âge*</label>
                <input type="text" name="age" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Email*</label>
                <input type="text" name="email" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Téléphone*</label>
                <input type="text" name="tel" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <button type="submit" class="tip-button">Confirmer inscription</button>
        </form>
    </div>
</div>
