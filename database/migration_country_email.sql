-- Migration: Add Country-Specific Email & FastAPI Configuration Columns
ALTER TABLE `countries`
ADD COLUMN IF NOT EXISTS `email_service_type` VARCHAR(50) NOT NULL DEFAULT 'global_default' AFTER `shipping_partner`,
ADD COLUMN IF NOT EXISTS `email_sender_name` VARCHAR(150) NULL AFTER `email_service_type`,
ADD COLUMN IF NOT EXISTS `email_sender_address` VARCHAR(191) NULL AFTER `email_sender_name`,
ADD COLUMN IF NOT EXISTS `email_api_endpoint` VARCHAR(255) NULL AFTER `email_sender_address`,
ADD COLUMN IF NOT EXISTS `email_api_key` VARCHAR(255) NULL AFTER `email_api_endpoint`,
ADD COLUMN IF NOT EXISTS `admin_notify_email` VARCHAR(191) NULL AFTER `email_api_key`;

