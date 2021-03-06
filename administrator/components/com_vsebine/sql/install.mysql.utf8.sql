CREATE TABLE IF NOT EXISTS `#__vsebine` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`title` VARCHAR(255)  NOT NULL ,
`introtext` VARCHAR(255)  NOT NULL ,
`fulltext` VARCHAR(255)  NOT NULL ,
`state` INT(11)  NOT NULL ,
`author` VARCHAR(255)  NOT NULL ,
`author_alias` VARCHAR(255)  NOT NULL ,
`created` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`tags` VARCHAR(255)  NOT NULL ,
`site_id` INT(11)  NOT NULL ,
`start_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`end_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
`lokacija` VARCHAR(255)  NOT NULL ,
`frontpage` TINYINT(4)  NOT NULL ,
`koledar` TINYINT(4)  NOT NULL ,
`slika` VARCHAR(255)  NOT NULL ,
PRIMARY KEY (`id`)
) DEFAULT COLLATE=utf8_general_ci;

