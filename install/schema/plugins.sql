DROP TABLE IF EXISTS `{DB_PREFIX}plugins`{SEPERATOR}
CREATE TABLE IF NOT EXISTS `{DB_PREFIX}plugins` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `version` varchar(5) NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(200) NOT NULL,
  `author` varchar(20) NOT NULL,
  `link` varchar(100) NOT NULL,
  `copyrights` varchar(100) NOT NULL,
  `access` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `active` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8{SEPERATOR}
DELETE FROM `{DB_PREFIX}plugins`{SEPERATOR}
INSERT INTO `{DB_PREFIX}plugins` (`id`, `name`, `version`, `title`, `description`, `author`, `link`, `copyrights`, `access`, `active`) VALUES (1, 'system', '1', 'system', '', 'system', '', '', 0, 1)