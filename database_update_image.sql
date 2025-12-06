-- Ajouter la colonne image_don à la table dons
-- Exécutez ce script dans phpMyAdmin ou votre client MySQL

ALTER TABLE `dons` 
ADD COLUMN `image_don` VARCHAR(255) NULL DEFAULT NULL AFTER `description_don`;

-- Vérifier que la colonne a été ajoutée
DESCRIBE `dons`;
