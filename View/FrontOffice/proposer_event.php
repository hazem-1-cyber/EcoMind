<?php
// proposer_event.php
?>
<div class="container">
    <header>
        <h1>Proposer un événement</h1>
        <p class="subtitle">Partagez votre initiative écologique avec la communauté</p>
    </header>

    <div class="tip-card">
        <div class="tip-icon"></div>
        <h3>Formulaire de proposition</h3>
        <p>Remplissez les informations ci-dessous pour proposer votre événement.</p>
        
        <form id="proposer-form" action="index.php?page=proposer" method="post" onsubmit="return confirmProposition();">
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Nom de l'association*</label>
                <input type="text" name="association_nom" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Contact e-mail*</label>
                <input type="text" name="email_contact" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Téléphone*</label>
                <input type="text" name="tel" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Type d'événement*</label>
                <input type="text" name="type" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; color: #013220; font-weight: bold;">Description*</label>
                <textarea name="description" rows="6" style="width: 100%; padding: 12px; border: 2px solid #A8E6CF; border-radius: 10px; font-size: 16px; resize: vertical;"></textarea>
            </div>
            
            <button type="submit" class="tip-button">Envoyer la proposition</button>
        </form>
    </div>
</div>
