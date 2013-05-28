<?php

// 000041
// добавить дату последнего обновления подписки в reader

// если модуль не установлен, ничего не делаем
if(!CModule::IsModuleInstalled('reader'))
{
	return true;
}

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/config.php');
$DB->Query("alter table `".READER_SUBSCRIPTION_TABLE."` add column `last_update` integer");

?>