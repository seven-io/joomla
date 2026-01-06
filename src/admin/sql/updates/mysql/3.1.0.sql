-- Version 3.1.0 - SMS Automation System

-- Automations table for SMS triggers and templates
CREATE TABLE IF NOT EXISTS `#__seven_automations`
(
    `id`               INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `title`            VARCHAR(255)        NOT NULL,
    `trigger_type`     VARCHAR(100)        NOT NULL,
    `enabled`          TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `template`         TEXT                NOT NULL,
    `sender_id`        VARCHAR(50)                  DEFAULT 'seven',
    `recipient_type`   VARCHAR(50)         NOT NULL DEFAULT 'customer',
    `custom_recipient` VARCHAR(255)                 DEFAULT NULL,
    `options`          TEXT                         DEFAULT NULL,
    `created`          DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `modified`         DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_trigger_type` (`trigger_type`),
    INDEX `idx_enabled` (`enabled`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

-- Automation execution logs
CREATE TABLE IF NOT EXISTS `#__seven_automation_logs`
(
    `id`             INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `automation_id`  INT(11) UNSIGNED    NOT NULL,
    `trigger_type`   VARCHAR(100)        NOT NULL,
    `recipient`      VARCHAR(255)        NOT NULL,
    `message`        TEXT                NOT NULL,
    `variables_used` TEXT                         DEFAULT NULL,
    `response_code`  SMALLINT(3) UNSIGNED         DEFAULT NULL,
    `response_data`  TEXT                         DEFAULT NULL,
    `success`        TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `created`        DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_automation_id` (`automation_id`),
    INDEX `idx_trigger_type` (`trigger_type`),
    INDEX `idx_created` (`created`),
    CONSTRAINT `fk_seven_automation` FOREIGN KEY (`automation_id`)
        REFERENCES `#__seven_automations` (`id`) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;
