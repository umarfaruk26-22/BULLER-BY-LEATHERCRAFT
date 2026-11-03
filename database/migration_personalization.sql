-- Migration: Add Personalization Fields to products table
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `has_personalization` TINYINT(1) NOT NULL DEFAULT 0 AFTER `care_instructions`,
ADD COLUMN IF NOT EXISTS `personalization_config` LONGTEXT NULL AFTER `has_personalization`;
