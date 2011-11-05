<?php

// 000022
// добавление холдов в кассу

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("CREATE TABLE `".KASSA_HOLD_TABLE."` (
  `id` integer UNSIGNED NOT NULL AUTO_INCREMENT,
  `operation_type_id` integer UNSIGNED NOT NULL,
  `sum` float  NOT NULL,
  `comment` varchar(255),
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM
CHARACTER SET utf8 COLLATE utf8_general_ci
COMMENT = 'Отложенные деньги'");
$res  = $DB->Query("select `id`, `access` from `".TREE_TABLE."` where `name` = 'kassa' and `module` = 'kassa' and `action` = ''");
$_row = $DB->Fetch($res);
if(!$_row['id'])
{
	return false;
}
$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$_row['id']."', 'hold', 'kassa', 'hold', '', '".$_row['access']."')");
return true;