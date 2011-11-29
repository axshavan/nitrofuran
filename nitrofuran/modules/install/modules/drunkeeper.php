<?

/*
	Установка модуля drunkeeper
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

global $DB;
require_once($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/modules/drunkeeper/config.php');
$DB->TransactionStart();

// создание таблиц
if(!$DB->Query("CREATE TABLE `".DRUNKEEPER_DRINKTYPES_TABLE."` (
	`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100)  NOT NULL,
	PRIMARY KEY (`id`)
  )
  ENGINE = InnoDB
  CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}
if(!$DB->Query("CREATE TABLE `".DRUNKEEPER_DRINKS_TABLE."` (
	`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100)  NOT NULL,
	`strength` double(4,2)  NOT NULL DEFAULT 0,
	`type_id` integer UNSIGNED NOT NULL,
	PRIMARY KEY (`id`)
  )
  ENGINE = InnoDB
  CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}
if(!$DB->Query("CREATE TABLE `".DRUNKEEPER_ACTS_TABLE."` (
	`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
	`drink_id` integer UNSIGNED NOT NULL,
	`volume` integer UNSIGNED NOT NULL,
	`date_drinked` integer UNSIGNED NOT NULL,
	`date_inserted` integer UNSIGNED NOT NULL,
	`comment` text  NOT NULL,
	PRIMARY KEY (`id`)
  )
  ENGINE = InnoDB
  CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}

// добавление в список модулей
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['drunkeeper'] = 'Бухлостатистика';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:5:"drunkeeper";s:30:"Бухлостатистика";}');
}

// меню настроек самого модуля
new_param('drunkeeper', 'admin_menu', 'Пункты админского меню', 'textarray', 'a:1:{i:1;s:49:"Настройки бухлостатистики";}');

// добавление путей в дерево папок
$r   = $DB->Query("select `id` from `".TREE_TABLE."` where `pid` = 0"); // по идее, это всегда 1, но мало ли...
$pid = $DB->Fetch($r);
$pid = $pid['id'];
if(!$pid)
{
	$DB->TransactionRollback();
	return false;
}
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'drunkeeper', 'drunkeeper', '', '', 0)");
$pid = $DB->InsertedId();
if(!$pid)
{
	$DB->TransactionRollback();
	return false;
}
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'edit', 'drunkeeper', '', '', 0)");

$DB->TransactionCommit();
return true;

?>