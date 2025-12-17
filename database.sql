-- =====================================================
-- BASE DE DONNÉES ECOMIND - TABLES ESSENTIELLES
-- =====================================================
-- Version: 1.0 (Minimal)
-- Description: Tables utilisées par le code existant
-- =====================================================

-- Créer la base de données
CREATE DATABASE IF NOT EXISTS `ecomind` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `ecomind`;

-- =====================================================
-- TABLE: dons (PRINCIPALE)
-- =====================================================
CREATE TABLE `dons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_don` varchar(50) DEFAULT 'money',
  `montant` decimal(10,2) DEFAULT NULL,
  `livraison` varchar(50) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `localisation` varchar(255) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `description_don` text DEFAULT NULL,
  `association_id` int(11) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'pending',
  `image_don` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_statut` (`statut`),
  KEY `idx_type_don` (`type_don`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: categories
-- =====================================================
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `nom` (`nom`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- TABLE: associations
-- =====================================================
CREATE TABLE `associations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DONNÉES INITIALES
-- =====================================================

-- Associations par défaut
INSERT INTO `associations` (`id`, `nom`, `description`, `email`) VALUES
(1, 'EcoMind Tunisie', 'Association principale pour la protection de l\'environnement en Tunisie', 'contact@ecomind.tn'),
(2, 'Jeunes Écologistes', 'Mouvement de jeunes pour l\'écologie', 'jeunes@ecomind.tn'),
(3, 'Sauvons nos Plages', 'Initiative de nettoyage des plages tunisiennes', 'plages@ecomind.tn');

-- Catégories par défaut
INSERT INTO `categories` (`id`, `nom`, `code`, `description`) VALUES
(1, 'Argent', 'money', 'Dons monétaires'),
(2, 'Panneaux Solaires', 'panneau_solaire', 'Équipements solaires'),
(3, 'Matériel', 'materiel', 'Matériel divers'),
(4, 'Électronique', 'electronique', 'Appareils électroniques'),
(5, 'Autre', 'autre', 'Autres types de dons');

-- =====================================================
-- INDEX ET CONTRAINTES SUPPLÉMENTAIRES
-- =====================================================

-- Index pour optimiser les requêtes
ALTER TABLE `dons` ADD INDEX `idx_association` (`association_id`);
ALTER TABLE `dons` ADD INDEX `idx_deleted_at` (`deleted_at`);

-- Contraintes de clés étrangères (optionnel)
-- ALTER TABLE `dons` ADD CONSTRAINT `fk_dons_association` FOREIGN KEY (`association_id`) REFERENCES `associations` (`id`) ON DELETE SET NULL;
