<?php

// 000006
// создание таблиц со свойствами операций и страницы с административным интерфейсом

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
global $DB;
$DB->TransactionStart();
if(!$DB->Query("CREATE TABLE `".KASSA_OPERATION_PROPVALUES_TABLE."` (
		`operation_type_id` integer UNSIGNED NOT NULL,
		`option_id` integer NOT NULL,
		`value` varchar(255) NOT NULL,
	PRIMARY KEY (`operation_type_id`, `option_id`)
	)
	ENGINE = InnoDB
	CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}
if(!$DB->Query("CREATE TABLE `".KASSA_OPERATION_PROPNAMES_TABLE."` (
		`id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
		`name` varchar(255) NOT NULL,
		`type` enum('text','checkbox') NOT NULL,
	PRIMARY KEY (`id`)
	)
	ENGINE = InnoDB
	CHARACTER SET utf8 COLLATE utf8_general_ci"))
{
	$DB->TransactionRollback();
	return false;
}
$_kassa_admin_menu    = unserialize(get_param('kassa', 'admin_menu'));
$_kassa_admin_menu[4] = 'Свойства типов операций';
set_param('kassa', 'admin_menu', serialize($_kassa_admin_menu));
$DB->TransactionCommit();
return true;

?>