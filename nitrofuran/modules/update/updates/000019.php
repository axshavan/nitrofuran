<?php

// 000019
// возможность проставить валюту и счёт по умолчанию

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("alter table `".KASSA_CURRENCY_TABLE."` add column `default` bool default false");
$DB->Query("alter table `".KASSA_ACCOUNT_TABLE."` add column `default` bool default false");

return true;

?>