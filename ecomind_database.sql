-- =====================================================
-- Base de données EcoMind - Gestion des Événements
-- Projet: Système de gestion d'événements écologiques
-- Auteur: Ranim
-- Date: Décembre 2024
-- =====================================================

-- Création de la base de données
CREATE DATABASE IF NOT EXISTS `ecomind_events` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecomind_events`;

-- =====================================================
-- TABLE: evenement
-- Description: Stocke les informations des événements écologiques
-- =====================================================
CREATE TABLE `evenement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `type` varchar(100) DEFAULT NULL,
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `image_url` varchar(500) DEFAULT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `date_evenement` datetime DEFAULT NULL,
  `organisateur` varchar(255) DEFAULT NULL,
  `duree` varchar(100) DEFAULT NULL,
  `statut` enum('actif','inactif','complet') DEFAULT 'actif',
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_date_creation` (`date_creation`),
  KEY `idx_statut` (`statut`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: inscription
-- Description: Stocke les inscriptions des participants aux événements
-- =====================================================
CREATE TABLE `inscription` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `evenement_id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `age` int(3) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `date_inscription` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('confirmee','en_attente','annulee') DEFAULT 'confirmee',
  `commentaires` text,
  PRIMARY KEY (`id`),
  KEY `fk_inscription_evenement` (`evenement_id`),
  KEY `idx_email` (`email`),
  KEY `idx_date_inscription` (`date_inscription`),
  CONSTRAINT `fk_inscription_evenement` FOREIGN KEY (`evenement_id`) REFERENCES `evenement` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: proposition
-- Description: Stocke les propositions d'événements soumises par les associations
-- =====================================================
CREATE TABLE `proposition` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `association_nom` varchar(255) NOT NULL,
  `email_contact` varchar(255) NOT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `type` varchar(100) DEFAULT NULL,
  `description` text NOT NULL,
  `lieu_propose` varchar(255) DEFAULT NULL,
  `date_proposition` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `statut` enum('en_attente','approuvee','rejetee') DEFAULT 'en_attente',
  `commentaires_admin` text,
  `date_traitement` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_type` (`type`),
  KEY `idx_date_proposition` (`date_proposition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: admin_users (optionnelle pour l'authentification)
-- Description: Stocke les comptes administrateurs
-- =====================================================
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL UNIQUE,
  `email` varchar(255) NOT NULL UNIQUE,
  `password_hash` varchar(255) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `role` enum('admin','moderateur') DEFAULT 'admin',
  `date_creation` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `derniere_connexion` timestamp NULL DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES D'EXEMPLE
-- =====================================================

-- Insertion d'événements d'exemple
INSERT INTO `evenement` (`titre`, `description`, `type`, `lieu`, `date_evenement`, `organisateur`, `duree`, `statut`) VALUES
('Clean-Up Day : Nettoyage de la plage', 'Collecte des déchets plastiques sur une plage locale. Organisateur : Équipe EcoMind Durée : 9h → 13h', 'Nettoyage', 'Plage de Sidi Bou Said', '2025-01-15 09:00:00', 'Équipe EcoMind', '4 heures', 'actif'),
('Plantation d\'Arbres au Parc Central', 'Reboisement du parc central avec des espèces locales. Une action pour lutter contre le réchauffement climatique.', 'Reboisement', 'Parc Central de Tunis', '2025-01-20 08:00:00', 'Association Verte Tunisie', '6 heures', 'actif'),
('Atelier Recyclage Créatif', 'Apprenez à transformer vos déchets en objets utiles et décoratifs. Matériel fourni.', 'Atelier', 'Centre Culturel El Manar', '2025-01-25 14:00:00', 'EcoMind Formation', '3 heures', 'actif'),
('Sensibilisation Écologique dans les Écoles', 'Programme de sensibilisation pour les élèves du primaire sur l\'importance de la protection environnementale.', 'Sensibilisation', 'École Primaire Ibn Khaldoun', '2025-02-01 10:00:00', 'Éducateurs EcoMind', '2 heures', 'actif'),
('Nettoyage des Berges du Lac', 'Action de nettoyage des berges du lac de Tunis avec tri sélectif des déchets collectés.', 'Nettoyage', 'Lac de Tunis', '2025-02-05 07:30:00', 'Volontaires EcoMind', '5 heures', 'actif');

-- Insertion d'inscriptions d'exemple
INSERT INTO `inscription` (`evenement_id`, `nom`, `prenom`, `age`, `email`, `tel`, `statut`) VALUES
(1, 'Ben Ali', 'Ahmed', 25, 'ahmed.benali@email.com', '+216 20 123 456', 'confirmee'),
(1, 'Trabelsi', 'Fatma', 30, 'fatma.trabelsi@email.com', '+216 22 234 567', 'confirmee'),
(1, 'Gharbi', 'Mohamed', 22, 'mohamed.gharbi@email.com', '+216 25 345 678', 'confirmee'),
(2, 'Sassi', 'Leila', 28, 'leila.sassi@email.com', '+216 23 456 789', 'confirmee'),
(2, 'Khelifi', 'Youssef', 35, 'youssef.khelifi@email.com', '+216 24 567 890', 'confirmee'),
(3, 'Bouazizi', 'Amina', 26, 'amina.bouazizi@email.com', '+216 21 678 901', 'confirmee'),
(3, 'Jemli', 'Karim', 31, 'karim.jemli@email.com', '+216 26 789 012', 'confirmee'),
(4, 'Mahfoudh', 'Sonia', 29, 'sonia.mahfoudh@email.com', '+216 27 890 123', 'confirmee'),
(5, 'Chedly', 'Rami', 24, 'rami.chedly@email.com', '+216 28 901 234', 'confirmee'),
(1, 'Hamdi', 'Nour', 27, 'nour.hamdi@email.com', '+216 29 012 345', 'confirmee');

-- Insertion de propositions d'exemple
INSERT INTO `proposition` (`association_nom`, `email_contact`, `tel`, `type`, `description`, `lieu_propose`, `statut`) VALUES
('Association Tunisie Verte', 'contact@tunisieverte.org', '+216 71 123 456', 'Reboisement', 'Proposition de plantation de 500 oliviers dans la région de Kairouan pour lutter contre la désertification.', 'Région de Kairouan', 'en_attente'),
('Jeunes Écologistes de Sfax', 'jeunes.eco.sfax@gmail.com', '+216 74 234 567', 'Nettoyage', 'Organisation d\'une journée de nettoyage du port de Sfax avec sensibilisation sur la pollution marine.', 'Port de Sfax', 'approuvee'),
('Club Environnement ISET', 'club.env.iset@edu.tn', '+216 70 345 678', 'Atelier', 'Atelier de fabrication de compost à partir de déchets organiques pour les étudiants et le personnel.', 'ISET Nabeul', 'en_attente'),
('Association Oasis Durable', 'oasis.durable@yahoo.fr', '+216 75 456 789', 'Sensibilisation', 'Campagne de sensibilisation sur l\'économie d\'eau dans les oasis du sud tunisien.', 'Tozeur et Nefta', 'approuvee'),
('Scouts Écologiques', 'scouts.eco@scouts.tn', '+216 71 567 890', 'Nettoyage', 'Nettoyage de la forêt de Ain Draham avec installation de panneaux de sensibilisation.', 'Forêt de Ain Draham', 'rejetee');

-- Insertion d'un utilisateur admin d'exemple (mot de passe: admin123)
INSERT INTO `admin_users` (`username`, `email`, `password_hash`, `nom`, `prenom`, `role`) VALUES
('admin', 'admin@ecomind.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrateur', 'EcoMind', 'admin');

-- =====================================================
-- VUES UTILES
-- =====================================================

-- Vue pour les statistiques des événements
CREATE VIEW `vue_stats_evenements` AS
SELECT 
    e.id,
    e.titre,
    e.type,
    e.date_creation,
    COUNT(i.id) as nb_inscriptions,
    e.statut
FROM evenement e
LEFT JOIN inscription i ON e.id = i.evenement_id
GROUP BY e.id, e.titre, e.type, e.date_creation, e.statut;

-- Vue pour les inscriptions avec détails événement
CREATE VIEW `vue_inscriptions_detaillees` AS
SELECT 
    i.id,
    i.nom,
    i.prenom,
    i.age,
    i.email,
    i.tel,
    i.date_inscription,
    i.statut as statut_inscription,
    e.titre as evenement_titre,
    e.type as evenement_type,
    e.date_evenement
FROM inscription i
JOIN evenement e ON i.evenement_id = e.id;

-- =====================================================
-- INDEX POUR OPTIMISATION
-- =====================================================

-- Index composites pour les recherches fréquentes
CREATE INDEX `idx_evenement_type_date` ON `evenement` (`type`, `date_creation`);
CREATE INDEX `idx_inscription_evenement_date` ON `inscription` (`evenement_id`, `date_inscription`);
CREATE INDEX `idx_proposition_statut_date` ON `proposition` (`statut`, `date_proposition`);

-- Index pour les recherches textuelles
CREATE FULLTEXT INDEX `idx_evenement_fulltext` ON `evenement` (`titre`, `description`);
CREATE FULLTEXT INDEX `idx_proposition_fulltext` ON `proposition` (`association_nom`, `description`);

-- =====================================================
-- PROCÉDURES STOCKÉES UTILES
-- =====================================================

DELIMITER //

-- Procédure pour obtenir les statistiques générales
CREATE PROCEDURE `GetStatistiquesGenerales`()
BEGIN
    SELECT 
        (SELECT COUNT(*) FROM evenement WHERE statut = 'actif') as total_evenements_actifs,
        (SELECT COUNT(*) FROM inscription WHERE statut = 'confirmee') as total_inscriptions,
        (SELECT COUNT(*) FROM proposition WHERE statut = 'en_attente') as propositions_en_attente,
        (SELECT COUNT(DISTINCT evenement_id) FROM inscription) as evenements_avec_inscriptions;
END //

-- Procédure pour nettoyer les anciennes données
CREATE PROCEDURE `NettoyerDonneesAnciennes`(IN `jours_anciennete` INT)
BEGIN
    -- Supprimer les propositions rejetées anciennes
    DELETE FROM proposition 
    WHERE statut = 'rejetee' 
    AND date_proposition < DATE_SUB(NOW(), INTERVAL jours_anciennete DAY);
    
    -- Marquer les événements anciens comme inactifs
    UPDATE evenement 
    SET statut = 'inactif' 
    WHERE date_evenement < DATE_SUB(NOW(), INTERVAL jours_anciennete DAY)
    AND statut = 'actif';
END //

DELIMITER ;

-- =====================================================
-- TRIGGERS POUR AUTOMATISATION
-- =====================================================

DELIMITER //

-- Trigger pour mettre à jour le statut d'un événement quand il est complet
CREATE TRIGGER `tr_check_evenement_complet` 
AFTER INSERT ON `inscription`
FOR EACH ROW
BEGIN
    DECLARE nb_inscriptions INT;
    DECLARE limite_inscriptions INT DEFAULT 50; -- Limite par défaut
    
    SELECT COUNT(*) INTO nb_inscriptions 
    FROM inscription 
    WHERE evenement_id = NEW.evenement_id AND statut = 'confirmee';
    
    IF nb_inscriptions >= limite_inscriptions THEN
        UPDATE evenement 
        SET statut = 'complet' 
        WHERE id = NEW.evenement_id;
    END IF;
END //

-- Trigger pour logger les modifications importantes
CREATE TRIGGER `tr_log_proposition_status_change`
AFTER UPDATE ON `proposition`
FOR EACH ROW
BEGIN
    IF OLD.statut != NEW.statut THEN
        INSERT INTO admin_logs (table_name, record_id, action_type, old_value, new_value, date_action)
        VALUES ('proposition', NEW.id, 'status_change', OLD.statut, NEW.statut, NOW());
    END IF;
END //

DELIMITER ;

-- =====================================================
-- TABLE DE LOGS (optionnelle)
-- =====================================================
CREATE TABLE `admin_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(50) NOT NULL,
  `record_id` int(11) NOT NULL,
  `action_type` varchar(50) NOT NULL,
  `old_value` text,
  `new_value` text,
  `date_action` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_table_record` (`table_name`, `record_id`),
  KEY `idx_date_action` (`date_action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CONFIGURATION ET PERMISSIONS
-- =====================================================

-- Création d'un utilisateur spécifique pour l'application (optionnel)
-- CREATE USER 'ecomind_user'@'localhost' IDENTIFIED BY 'ecomind_password_2024';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON ecomind_events.* TO 'ecomind_user'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- NOTES D'UTILISATION
-- =====================================================
/*
UTILISATION DE CETTE BASE DE DONNÉES:

1. CONFIGURATION:
   - Modifier config.php avec vos paramètres de connexion
   - Adapter les constantes DB_HOST, DB_NAME, DB_USER, DB_PASS

2. FONCTIONNALITÉS PRINCIPALES:
   - Gestion complète des événements écologiques
   - Système d'inscription avec confirmation par email
   - Propositions d'événements par les associations
   - Interface d'administration avec recherche avancée
   - Statistiques et rapports

3. SÉCURITÉ:
   - Mots de passe hashés avec password_hash()
   - Validation des données côté serveur
   - Protection contre les injections SQL avec PDO
   - Logs d'activité pour traçabilité

4. PERFORMANCE:
   - Index optimisés pour les requêtes fréquentes
   - Vues pour simplifier les requêtes complexes
   - Procédures stockées pour les opérations répétitives

5. MAINTENANCE:
   - Utiliser la procédure NettoyerDonneesAnciennes() régulièrement
   - Surveiller les logs d'erreur
   - Sauvegarder régulièrement la base de données

6. EXTENSIONS POSSIBLES:
   - Système de notifications push
   - Géolocalisation des événements
   - Système de notation/commentaires
   - API REST pour applications mobiles
   - Intégration avec réseaux sociaux
*/

-- =====================================================
-- FIN DU SCRIPT
-- =====================================================