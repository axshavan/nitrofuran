<?php

/*
 * Установка модуля kassa заключается, в основном, только в модификации
 * базы данных:
 * - добавление путей в таблицу виртуального дерева;
 * - добавление кассы в параметр modules_installed админки;
 * - добавление параметров модуля кассы;
 * - создание необходимых таблиц;
 * - добавление некоторых данных "по умолчанию" в созданные таблицы.
 *
 * Конечно, по-хорошему, стоило бы про все эти параметры, которые устанавливаются
 * по умолчанию, спрашивать при установке у пользователя...
 * 
 */

global $DB;
$DB->TransactionStart();

// добавление путей в таблицу виртуального дерева
$r   = $DB->Query("select `id` from `".TREE_TABLE."` where `pid` = 0"); // по идее, это всегда 1, но мало ли...
$pid = $DB->Fetch($r);
$pid = $pid['id'];
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'kassa', 'kassa', '', '', 1)");
$pid = $DB->InsertedId();
if(!$pid)
{
	$DB->TransactionRollback();
	// обработка ошибки
	// ...
	die();
}
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'add', 'kassa', 'add', '', 1)");
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'edit', 'kassa', 'edit', '' ,1)");
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'stats', 'kassa', 'stats', '', 1)");
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'tran_ac', 'kassa', 'tran_ac', '', 1)");

// создание необходимых таблиц
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("DROP TABLE IF EXISTS `".KASSA_ACCOUNT_TABLE."`");
$DB->Query("CREATE TABLE `".KASSA_ACCOUNT_TABLE."` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$DB->Query("DROP TABLE IF EXISTS `".KASSA_CURRENCY_TABLE."`");
	$DB->Query("CREATE TABLE `".KASSA_CURRENCY_TABLE."` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`symbol` char(1) NOT NULL,
	`name` varchar(32) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$DB->Query("DROP TABLE IF EXISTS `".KASSA_OPERATION_TABLE."`");
$DB->Query("CREATE TABLE `".KASSA_OPERATION_TABLE."` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`currency_id` int(10) unsigned NOT NULL,
	`account_id` int(10) unsigned NOT NULL,
	`type_id` int(10) unsigned NOT NULL,
	`amount` double NOT NULL,
	`time` int(10) unsigned NOT NULL,
	`comment` varchar(255) DEFAULT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$DB->Query("DROP TABLE IF EXISTS `".KASSA_OPERATION_TYPE_TABLE."`");
$DB->Query("CREATE TABLE `".KASSA_OPERATION_TYPE_TABLE."` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`group_id` int(10) unsigned NOT NULL,
	`name` varchar(64) NOT NULL,
	`is_income` tinyint(1) unsigned NOT NULL DEFAULT '0',
	`is_service` tinyint(1) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$DB->Query("DROP TABLE IF EXISTS `".KASSA_OPERATION_TYPE_GROUP_TABLE."`");
$DB->Query("CREATE TABLE `".KASSA_OPERATION_TYPE_GROUP_TABLE."` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(64) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");
$DB->Query("DROP TABLE IF EXISTS `".KASSA_PLANS_TABLE."`");
$DB->Query("CREATE TABLE `".KASSA_PLANS_TABLE."` (
	`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
	`operation_type_id` int(10) unsigned NOT NULL,
	`amount` double(10,2) NOT NULL,
	`repeat_type` enum('none','daily','weekly','monthly') NOT NULL,
	`repeat` varchar(32) DEFAULT NULL,
	`active` tinyint(1) NOT NULL DEFAULT '1',
	`name` varchar(64) NOT NULL,
	PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8");

// добавление некоторых данных "по умолчанию" в созданные таблицы
$DB->Query("insert into `".KASSA_OPERATION_TYPE_GROUP_TABLE."` (`name`) values ('Прибыль')");
$income_id = $DB->InsertedId();
if(!$income_id)
{
	$DB->TransactionRollback();
	// обработка ошибки
	// ...
	die();
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_GROUP_TABLE."` (`name`) values ('Прочее')");
$other_id = $DB->InsertedId();
if(!$other_id)
{
	$DB->TransactionRollback();
	// обработка ошибки
	// ...
	die();
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$income_id."', 'Перенос с другого счёта', 1, 1)");
$OPTYPE_TRANSACTION_FROM_ID = $DB->InsertedId();
if(!$OPTYPE_TRANSACTION_FROM_ID)
{
	$DB->TransactionRollback();
	// обработка ошибки
	// ...
	die();
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$other_id."', 'Перенос на другой счёт', 0, 1)");
$OPTYPE_TRANSACTION_TO_ID = $DB->InsertedId();
if(!$OPTYPE_TRANSACTION_TO_ID)
{
	$DB->TransactionRollback();
	// обработка ошибки
	// ...
	die();
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$other_id."', 'Комиссия', 0, 1)");
$OPTYPE_TRANSACTION_COMISSION_ID = $DB->InsertedId();
if(!$OPTYPE_TRANSACTION_COMISSION_ID)
{
	$DB->TransactionRollback();
	// обработка ошибки
	// ...
	die();
}
$DB->Query("insert into `".KASSA_ACCOUNT_TABLE."` (`name`) values ('Наличные')");
$DB->Query("insert into `".KASSA_CURRENCY_TABLE."` (`symbol`, `name`) values ('р', 'рубль')");

// добавление кассы в параметр modules_installed админки
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['kassa'] = 'Касса';
}
else
{
	$r = array('kassa' => 'Касса');
}
$r = serialize($r);
set_param('admin', 'modules_installed', $r);

// добавление параметров модуля кассы
set_param('kassa', 'OPTYPE_TRANSACTION_FROM_ID',      $OPTYPE_TRANSACTION_FROM_ID);
set_param('kassa', 'OPTYPE_TRANSACTION_TO_ID',        $OPTYPE_TRANSACTION_TO_ID);
set_param('kassa', 'OPTYPE_TRANSACTION_COMISSION_ID', $OPTYPE_TRANSACTION_COMISSION_ID);

set_param('kassa', 'admin_menu',  'a:2:{i:1;s:29:\"Настройки кассы\";i:2;s:24:\"Планирование\";}');
set_param('kassa', 'stats_title', 'Статистика');
set_param('kassa', 'title',       'Касса');

$DB->TransactionCommit();

?>