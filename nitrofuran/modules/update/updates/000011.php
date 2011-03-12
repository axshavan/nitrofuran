<?php

// 000011
// добавление валют в планирование в кассе

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("alter table `".KASSA_PLANS_TABLE."` add column `currency_id` integer unsigned not null");
$DB->Query("update `".KASSA_PLANS_TABLE."` set `currency_id` = 1");

?>