CREATE TABLE IF NOT EXISTS `#__sms77api_configurations`
(
    `id`        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `api_key`     VARCHAR(255)        NOT NULL,
    `updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `published` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0',
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDb;

CREATE TABLE IF NOT EXISTS `#__sms77api_messages`
(
    `id`        INT(11) UNSIGNED    NOT NULL AUTO_INCREMENT,
    `response`     TEXT         DEFAULT NULL,
    `config` TEXT NOT NULL,
    `created` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDb;