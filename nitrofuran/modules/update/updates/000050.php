<?php

// 000050
// добавление свойства "предупреждать о уходе в минус" для аккаунта в кассе
if(!CModule::IsModuleInstalled('kassa'))
{
	return true;
}

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->TransactionStart();

if(!$DB->Query("alter table `".KASSA_ACCOUNT_TABLE."` add column `warnlimit` bool default 0"))
{
	$DB->TransactionRollback();
	return false;
}
$DB->TransactionCommit();
return true;

?>