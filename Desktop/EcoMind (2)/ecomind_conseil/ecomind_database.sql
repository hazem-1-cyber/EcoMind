-- =====================================================
-- Base de données EcoMind - Système d'évaluation écologique
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS `ecomind` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecomind`;

-- =====================================================
-- Table: reponse_formulaire
-- Stocke les réponses des utilisateurs au formulaire
-- =====================================================
CREATE TABLE `reponse_formulaire` (
  `idformulaire` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nb_personnes` int(11) NOT NULL DEFAULT 1,
  `douche_freq` int(11) NOT NULL DEFAULT 7,
  `douche_duree` int(11) NOT NULL DEFAULT 10,
  `chauffage` varchar(50) NOT NULL DEFAULT 'electrique',
  `temp_hiver` int(11) NOT NULL DEFAULT 20,
  `transport_travail` varchar(255) NOT NULL DEFAULT 'voiture',
  `distance_travail` int(11) NOT NULL DEFAULT 0,
  `date_soumission` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`idformulaire`),
  KEY `idx_email` (`email`),
  KEY `idx_date` (`date_soumission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: conseil
-- Stocke les conseils écologiques prédéfinis
-- =====================================================
CREATE TABLE `conseil` (
  `idconseil` int(11) NOT NULL AUTO_INCREMENT,
  `type` enum('eau','energie','transport') NOT NULL,
  `titre` varchar(255) NOT NULL,
  `texte_conseil` text NOT NULL,
  `impact_estime` varchar(100) DEFAULT NULL,
  `difficulte` enum('facile','moyen','difficile') DEFAULT 'facile',
  `date_creation` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actif` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`idconseil`),
  KEY `idx_type` (`type`),
  KEY `idx_actif` (`actif`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Insertion des conseils par défaut
-- =====================================================

-- Conseils EAU
INSERT INTO `conseil` (`type`, `titre`, `texte_conseil`, `impact_estime`, `difficulte`) VALUES
('eau', 'Douches plus courtes', 'Réduisez vos douches à 5 minutes maximum. Utilisez un minuteur pour vous aider !', '30% d\'économie d\'eau', 'facile'),
('eau', 'Récupération d\'eau de pluie', 'Installez un système de récupération d\'eau de pluie pour arroser vos plantes.', '20% d\'économie d\'eau', 'moyen'),
('eau', 'Robinets économiques', 'Installez des mousseurs sur vos robinets pour réduire le débit sans perdre en confort.', '15% d\'économie d\'eau', 'facile'),
('eau', 'Réparation des fuites', 'Vérifiez et réparez toutes les fuites d\'eau dans votre logement.', '10% d\'économie d\'eau', 'moyen'),
('eau', 'Lave-vaisselle éco', 'Utilisez votre lave-vaisselle uniquement quand il est plein et en mode éco.', '25% d\'économie d\'eau', 'facile');

-- Conseils ÉNERGIE
INSERT INTO `conseil` (`type`, `titre`, `texte_conseil`, `impact_estime`, `difficulte`) VALUES
('energie', 'Température optimale', 'Baissez votre chauffage de 1°C, vous économiserez 7% d\'énergie !', '7% par degré', 'facile'),
('energie', 'Isolation thermique', 'Améliorez l\'isolation de votre logement : combles, murs, fenêtres.', '30% d\'économie', 'difficile'),
('energie', 'Appareils en veille', 'Débranchez vos appareils électroniques au lieu de les laisser en veille.', '10% d\'économie', 'facile'),
('energie', 'Éclairage LED', 'Remplacez toutes vos ampoules par des LED basse consommation.', '80% d\'économie sur l\'éclairage', 'facile'),
('energie', 'Programmateur chauffage', 'Installez un programmateur pour adapter le chauffage à vos horaires.', '15% d\'économie', 'moyen');

-- Conseils TRANSPORT
INSERT INTO `conseil` (`type`, `titre`, `texte_conseil`, `impact_estime`, `difficulte`) VALUES
('transport', 'Vélo ou marche', 'Pour les trajets de moins de 5km, privilégiez le vélo ou la marche.', '100% moins de CO2', 'facile'),
('transport', 'Transports en commun', 'Utilisez les transports en commun pour vos trajets quotidiens.', '75% moins de CO2', 'facile'),
('transport', 'Covoiturage', 'Organisez du covoiturage avec vos collègues ou utilisez des apps dédiées.', '50% moins de CO2', 'facile'),
('transport', 'Télétravail', 'Négociez du télétravail pour réduire vos déplacements domicile-travail.', '20% moins de trajets', 'moyen'),
('transport', 'Conduite éco', 'Adoptez une conduite souple : anticipez, respectez les limitations.', '20% d\'économie de carburant', 'facile');

-- =====================================================
-- Table: admin_users (optionnelle)
-- Pour la gestion des administrateurs
-- =====================================================
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Créer un admin par défaut (mot de passe: admin123)
INSERT INTO `admin_users` (`username`, `password`, `email`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@ecomind.com');

-- =====================================================
-- Vues utiles pour les statistiques
-- =====================================================

-- Vue: Statistiques générales
CREATE VIEW `stats_generales` AS
SELECT 
    COUNT(*) as total_reponses,
    AVG(nb_personnes) as moyenne_personnes,
    AVG(douche_freq) as moyenne_douches_semaine,
    AVG(douche_duree) as moyenne_duree_douche,
    AVG(temp_hiver) as moyenne_temperature,
    AVG(distance_travail) as moyenne_distance_travail
FROM reponse_formulaire;

-- Vue: Répartition des types de chauffage
CREATE VIEW `stats_chauffage` AS
SELECT 
    chauffage,
    COUNT(*) as nombre,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reponse_formulaire), 2) as pourcentage
FROM reponse_formulaire 
GROUP BY chauffage 
ORDER BY nombre DESC;

-- Vue: Répartition des moyens de transport
CREATE VIEW `stats_transport` AS
SELECT 
    transport_travail,
    COUNT(*) as nombre,
    ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM reponse_formulaire), 2) as pourcentage
FROM reponse_formulaire 
GROUP BY transport_travail 
ORDER BY nombre DESC;

-- =====================================================
-- Index pour optimiser les performances
-- =====================================================

-- Index sur les colonnes fréquemment utilisées
ALTER TABLE `reponse_formulaire` ADD INDEX `idx_chauffage` (`chauffage`);
ALTER TABLE `reponse_formulaire` ADD INDEX `idx_transport` (`transport_travail`);
ALTER TABLE `reponse_formulaire` ADD INDEX `idx_nb_personnes` (`nb_personnes`);

-- =====================================================
-- Données d'exemple (optionnel)
-- =====================================================

-- Quelques réponses d'exemple pour tester
INSERT INTO `reponse_formulaire` 
(`email`, `nb_personnes`, `douche_freq`, `douche_duree`, `chauffage`, `temp_hiver`, `transport_travail`, `distance_travail`) 
VALUES
('exemple1@test.com', 4, 7, 10, 'electrique', 20, 'voiture', 15),
('exemple2@test.com', 2, 5, 8, 'gaz', 19, 'transport_commun', 25),
('exemple3@test.com', 3, 6, 12, 'pompe_a_chaleur', 21, 'velo', 5),
('exemple4@test.com', 1, 4, 6, 'bois', 18, 'marche', 2),
('exemple5@test.com', 5, 10, 15, 'electrique', 22, 'voiture', 30);

-- =====================================================
-- Procédures stockées utiles
-- =====================================================

DELIMITER //

-- Procédure pour nettoyer les anciennes réponses (plus de 1 an)
CREATE PROCEDURE CleanOldResponses()
BEGIN
    DELETE FROM reponse_formulaire 
    WHERE date_soumission < DATE_SUB(NOW(), INTERVAL 1 YEAR);
    
    SELECT ROW_COUNT() as deleted_rows;
END //

-- Fonction pour calculer l'empreinte carbone approximative
CREATE FUNCTION CalculateFootprint(
    nb_pers INT,
    douche_f INT,
    douche_d INT,
    temp INT,
    dist INT
) RETURNS DECIMAL(10,2)
READS SQL DATA
DETERMINISTIC
BEGIN
    DECLARE footprint DECIMAL(10,2);
    
    -- Calcul simplifié basé sur les paramètres
    SET footprint = (
        (nb_pers * 0.5) +                    -- Base par personne
        (douche_f * douche_d * 0.1) +        -- Consommation eau
        ((temp - 18) * 2) +                  -- Chauffage
        (dist * 0.2)                         -- Transport
    );
    
    RETURN footprint;
END //

DELIMITER ;

-- =====================================================
-- Triggers pour l'audit
-- =====================================================

-- Trigger pour logger les nouvelles réponses
CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `action` varchar(10) NOT NULL,
  `record_id` int(11) NOT NULL,
  `timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER //

CREATE TRIGGER audit_reponse_insert
AFTER INSERT ON reponse_formulaire
FOR EACH ROW
BEGIN
    INSERT INTO audit_log (table_name, action, record_id, details)
    VALUES ('reponse_formulaire', 'INSERT', NEW.idformulaire, 
            CONCAT('Nouvelle réponse de: ', NEW.email));
END //

DELIMITER ;

-- =====================================================
-- Permissions et sécurité
-- =====================================================

-- Créer un utilisateur spécifique pour l'application (optionnel)
-- CREATE USER 'ecomind_app'@'localhost' IDENTIFIED BY 'mot_de_passe_securise';
-- GRANT SELECT, INSERT, UPDATE ON ecomind.* TO 'ecomind_app'@'localhost';
-- GRANT DELETE ON ecomind.reponse_formulaire TO 'ecomind_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- Fin du script
-- =====================================================

-- Afficher un résumé
SELECT 'Base de données EcoMind créée avec succès!' as message;
SELECT COUNT(*) as conseils_inseres FROM conseil;
SELECT COUNT(*) as reponses_exemple FROM reponse_formulaire;