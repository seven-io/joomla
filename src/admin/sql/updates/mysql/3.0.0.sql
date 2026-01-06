-- Version 3.0.0 - Joomla 5 migration with OAuth

-- Update messages table structure
DROP TABLE IF EXISTS `#__seven_messages`;
CREATE TABLE IF NOT EXISTS `#__seven_messages`
(
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `recipient`     VARCHAR(255)     NOT NULL,
    `text`          TEXT             NOT NULL,
    `sender`        VARCHAR(50)               DEFAULT NULL,
    `response_code` SMALLINT(3) UNSIGNED      DEFAULT NULL,
    `response_data` TEXT                      DEFAULT NULL,
    `created`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_recipient` (`recipient`),
    INDEX `idx_created` (`created`)
) ENGINE = InnoDB;

-- Update voices table structure
DROP TABLE IF EXISTS `#__seven_voices`;
CREATE TABLE IF NOT EXISTS `#__seven_voices`
(
    `id`            INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `recipient`     VARCHAR(255)     NOT NULL,
    `text`          TEXT             NOT NULL,
    `sender`        VARCHAR(50)               DEFAULT NULL,
    `response_code` SMALLINT(3) UNSIGNED      DEFAULT NULL,
    `seven_id`      INT(11) UNSIGNED          DEFAULT NULL,
    `eur`           DECIMAL(6,4) UNSIGNED     DEFAULT NULL,
    `created`       DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_recipient` (`recipient`),
    INDEX `idx_created` (`created`)
) ENGINE = InnoDB;
