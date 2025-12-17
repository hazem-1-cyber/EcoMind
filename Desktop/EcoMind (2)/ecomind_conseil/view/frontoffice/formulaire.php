<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire Écologique</title>
    <link rel="stylesheet" href="conseil.css">
    <link rel="stylesheet" href="formulaire.css">
</head>
<body>
    <div class="container">
        <header>
            <img src="assets/images/Screenshot_2025-11-16_152042-removebg-preview.png" alt="Logo Eco Mind" class="logo-eco">
            <h1>Formulaire Écologique</h1>
        </header>

        <!-- UN SEUL FORM, AVEC ACTION ET METHOD -->
        <form action="traitement_formulaire.php" method="post" id="form-eco" novalidate>

            <!-- Section Eau -->
            <section class="form-section">
                <h2>Consommation d'Eau</h2>
                <div class="form-group">
                    <label for="douche-freq">Fréquence des douches par semaine</label>
                    <input type="text" id="douche-freq" name="douche_freq" placeholder="Ex: 7">
                </div>
                <div class="form-group">
                    <label for="douche-duree">Durée moyenne d'une douche (minutes)</label>
                    <input type="text" id="douche-duree" name="douche_duree" placeholder="Ex: 10">
                </div>
            </section>

            <!-- Section Énergie -->
            <section class="form-section">
                <h2>Consommation d'Énergie</h2>
                <div class="form-group">
                    <label for="chauffage">Type de chauffage principal</label>
                    <select id="chauffage" name="chauffage">
                        <option value="">Choisissez...</option>
                        <option value="electrique">Électrique</option>
                        <option value="gaz">Gaz</option>
                        <option value="bois">Bois</option>
                        <option value="pompe_a_chaleur">Pompe à chaleur</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="temp-hiver">Température moyenne en hiver (°C)</label>
                    <input type="text" id="temp-hiver" name="temp_hiver" placeholder="Ex: 20">
                </div>
            </section>

            <!-- Section Transport -->
            <section class="form-section">
                <h2>Transport</h2>
                <div class="form-group">
                    <label for="transport-travail">Moyen de transport principal</label>
                    <input type="text" id="transport-travail" name="transport_travail" placeholder="Ex: voiture, vélo, bus...">
                </div>
                <div class="form-group">
                    <label for="distance-travail">Distance domicile-travail (km)</label>
                    <input type="text" id="distance-travail" name="distance_travail" placeholder="Ex: 15">
                </div>
            </section>

            <!-- Section Infos perso -->
            <section class="form-section">
                <h2>Informations Personnelles</h2>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="Ex: nom@exemple.com">
                </div>
                <div class="form-group">
                    <label for="nb-personnes">Nombre de personnes dans le foyer</label>
                    <input type="text" id="nb-personnes" name="nb_personnes" placeholder="Ex: 4">
                </div>
            </section>

            <!-- Info IA -->
            <section class="form-section" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.1)); border: 2px solid #667eea;">
                <div style="text-align: center;">
                    <h2 style="color: #667eea; margin-bottom: 15px;">🤖 Conseils Générés par Intelligence Artificielle</h2>
                    <div style="background: rgba(255, 255, 255, 0.9); padding: 20px; border-radius: 15px; box-shadow: 0 4px 15px rgba(102, 126, 234, 0.2);">
                        <p style="margin: 0; color: #013220; font-size: 1.05em; line-height: 1.8;">
                            ✨ <strong>Nouveau !</strong> Vos conseils seront automatiquement générés par notre intelligence artificielle.
                            <br><br>
                            🎯 L'IA analysera vos réponses pour créer des conseils <strong>ultra-personnalisés</strong> et parfaitement adaptés à votre situation unique.
                            <br><br>
                            💡 Chaque conseil sera unique et conçu spécialement pour vous aider à réduire votre empreinte écologique de manière efficace !
                        </p>
                        <a href="info_ia.html" style="display: inline-block; margin-top: 15px; padding: 10px 25px; background: #667eea; color: white; text-decoration: none; border-radius: 25px; font-size: 0.95em; transition: all 0.3s;">
                            En savoir plus sur l'IA →
                        </a>
                    </div>
                </div>
            </section>

            <!-- Boutons -->
            <div class="form-actions">
                <button type="submit">🌱 Obtenir mes conseils personnalisés</button>
                <button type="reset">Réinitialiser</button>
            </div>
        </form>

    </div>

    <?php include '../common/footer.php'; ?>

    <script src="formulaire.js"></script>
    <form id="form-eco" action="traitement_formulaire.php" method="post"></form>
</body>
</html>