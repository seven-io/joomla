CREATE TABLE IF NOT EXISTS `#__sms77api_configurations`
(
    `api_key`   VARCHAR(255)        NOT NULL,
    `id`        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    `updated`   DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
) ENGINE = InnoDb;

CREATE TABLE IF NOT EXISTS `#__sms77api_messages`
(
    `config`   TEXT             NOT NULL,
    `created`  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `id`       INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `response` TEXT                      DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE = InnoDb;