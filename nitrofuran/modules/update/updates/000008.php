<?php

// 000008
// привязка свойств операций по коду в кассе

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("alter table `".KASSA_OPERATION_PROPNAMES_TABLE."` add column `code` varchar(25) not null");
$DB->Query("update `".KASSA_OPERATION_PROPNAMES_TABLE."` set `code` = 'showinplans' where `id` = 1");

?>