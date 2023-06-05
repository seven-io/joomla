CREATE TABLE IF NOT EXISTS `#__seven_configurations`
(
    `api_key`   VARCHAR(255)        NOT NULL,
    `id`        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `updated`   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDb;

CREATE TABLE IF NOT EXISTS `#__seven_messages`
(
    `config`   TEXT             NOT NULL,
    `created`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `response` TEXT                      DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDb;

CREATE TABLE IF NOT EXISTS `#__seven_voices`
(
    `code`     SMALLINT(3) UNSIGNED DEFAULT NULL,
    `config`   TEXT             NOT NULL,
    `created`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `eur`      DECIMAL(4,2) UNSIGNED DEFAULT NULL,
    `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `seven_id` INT(11) UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE = InnoDb;

