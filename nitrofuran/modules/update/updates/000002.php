<?php

// 000002
// обновление структуры и данных для учёта долгов в кассе

global $DB;

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');

// создание таблиц
$DB->Query("CREATE TABLE `kassa_debtors` (
  `id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(64)  NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci");
$DB->Query("CREATE TABLE `kassa_debtors_operation` (
  `id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
  `debtor_id` integer UNSIGNED NOT NULL,
  `date` integer UNSIGNED NOT NULL,
  `amount` double(10,2)  NOT NULL,
  `currency_id` integer UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_general_ci");

// ещё одна виртуальная папочка
$res  = $DB->Query("select `id` from `".TREE_TABLE."` where `module` = 'kassa' and `action` = ''");
$_row = $DB->Fetch($res);
$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$_row['id']."', 'debtor', 'kassa', 'debtor', '', 1)");

// страница в административной части
$_pages = unserialize(get_param('kassa', 'admin_menu'));
$_pages[3] = 'Должники и кредиторы';
set_param('kassa', 'admin_menu', serialize($_pages));

// тип операций "взял денег"
$res  = $DB->Query("select `id` from `".KASSA_OPERATION_TYPE_GROUP_TABLE."` where `name` = 'Прибыль'");
$_row = $DB->Fetch($res);
$group_id = $_row['id'];
if(!$group_id)
{
	$group_id = 1;
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$group_id."', 'Взял денег', 1, 0)");
$insert_id = $DB->InsertedId();
if(!$insert_id)
{
	return false;
}
new_param('kassa', 'OPTYPE_DEBTOR_DEBIT', 'Взял денег', 'text', $insert_id);

// тип операций "дал денег"
$res  = $DB->Query("select `id` from `".KASSA_OPERATION_TYPE_GROUP_TABLE."` where `name` = 'Прочее'");
$_row = $DB->Fetch($res);
$group_id = $_row['id'];
if(!$group_id)
{
	$group_id = 1;
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$group_id."', 'Дал денег', 0, 0)");
$insert_id = $DB->InsertedId();
if(!$insert_id)
{
	return false;
}
new_param('kassa', 'OPTYPE_DEBTOR_CREDIT', 'Дал денег', 'text', $insert_id);

return true;

?>