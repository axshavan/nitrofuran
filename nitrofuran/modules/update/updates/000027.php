<?php

// 000027
// сокрытие счетов в кассе

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->TransactionStart();
$DB->Query("alter table `".KASSA_ACCOUNT_TABLE."` add column `show` bool default 1");
$DB->TransactionCommit();
return true;

?>