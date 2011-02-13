<?php

/*
	Установка модуля static.
*/

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/static/config.php');
$DB->Query("CREATE TABLE `".STATIC_PAGES_TABLE."` (
	`id` integer  NOT NULL AUTO_INCREMENT,
	`tree_id` integer,
	`content` text  NOT NULL,
	PRIMARY KEY  USING BTREE(`id`, `tree_id`)
  )
  ENGINE = InnoDB");

// добавление static в параметр modules_installed админки
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['static'] = 'Статичные страницы';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:6:"static";s:35:"Статичные страницы";}');
}

?>