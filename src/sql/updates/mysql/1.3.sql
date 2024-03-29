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
