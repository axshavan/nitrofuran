<?php

/*
 * Установка модуля для хранения паролей и всяких прочих параметров доступа
 * к разным местам. Здесь создаются папки виртуального дерева и создаются
 * соответствующие таблицы.
 */

global $DB;
$DB->TransactionStart();

// добавление папок виртуального дерева
$res     = $DB->Query("select `id` from `".TREE_TABLE."` where `pid` = 0");
$root_id = $DB->Fetch($res);
$root_id = $root_id['id'];
$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$root_id."', 'kokol', 'kokol', '', '', 1)");
$root_id = $DB->InsertedId();
$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$root_id."', 'edit', 'kokol', 'edit', '', 1)");

// создание таблиц
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kokol/config.php');
$DB->Query("CREATE TABLE `".KOKOL_CATEGORIES_TABLE."` (
  `id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` integer UNSIGNED NOT NULL,
  `name` varchar(255)  NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;");
$DB->Query("CREATE TABLE `".KOKOL_PASSWORDS_TABLE."` (
  `id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id` integer UNSIGNED NOT NULL,
  `resource` varchar(255)  NOT NULL,
  `login` varchar(255)  NOT NULL,
  `password` varchar(255)  NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci;");

// добавление модуля в установленные
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['kokol'] = 'Пароли';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:5:"kokol";s:12:"Пароли";}');
}

$DB->TransactionCommit();

?>