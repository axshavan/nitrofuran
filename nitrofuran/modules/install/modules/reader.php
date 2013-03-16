<?

/**
 * Установка модуля reader
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/config.php');
$DB->TransactionStart();

// создание таблиц
if(!$DB->Query("CREATE TABLE `".READER_SUBSCRIPTION_GROUP_TABLE."` (
	`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`group_id` integer default '0',
	PRIMARY KEY (`id`)
  )
  ENGINE = InnoDB
  CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}
if(!$DB->Query("CREATE TABLE `".READER_SUBSCRIPTION_TABLE."` (
	`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(100) NOT NULL,
	`group_id` integer default '0',
	`user_id` integer NOT NULL,
	PRIMARY KEY (`id`)
  )
  ENGINE = InnoDB
  CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}
if(!$DB->Query("CREATE TABLE `".READER_SUBSCRIPTION_ITEM_TABLE."` (
	`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`subscription_id` integer NOT NULL,
	`href` varchar(2048) NOT NULL,
	`read_flag` integer NOT NULL default 0,
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
	$r['reader'] = 'Ридер';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:6:"reader";s:10:"Ридер";}');
}

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
	('".$pid."', 'reader', 'reader', '', '', 0)");
$pid = $DB->InsertedId();
if(!$pid)
{
	$DB->TransactionRollback();
	return false;
}
$DB->TransactionCommit();
return true;

?>