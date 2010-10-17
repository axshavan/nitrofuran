DROP TABLE IF EXISTS `params`;
CREATE TABLE `params` (
  `module` varchar(32) NOT NULL,
  `name` varchar(64) NOT NULL,
  `type` enum('text','textarray','textarea') NOT NULL DEFAULT 'text',
  `display_name` varchar(64) DEFAULT NULL,
  `value` text,
  PRIMARY KEY (`module`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE  `sessions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `md5id` varchar(32) NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `last_action` int(10) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `remember` tinyint(3) unsigned NOT NULL,
  `bind2ip` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`,`md5id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `tree`;
CREATE TABLE `tree` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `module` varchar(50) NOT NULL,
  `action` varchar(50) NOT NULL,
  `template` varchar(50) NOT NULL,
  `access` tinyint(3) unsigned DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `tree` VALUES  (1,0,'','','','',1),
 (2,1,'admin','admin','','',1);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(32) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(32) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `regdate` int(10) unsigned NOT NULL,
  `authkey` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;