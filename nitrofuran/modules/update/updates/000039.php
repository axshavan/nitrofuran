<?php

// 000039
// добавить дату в таблицу с элементами подписки

// если модуль не установлен, ничего не делаем
if(!CModule::IsModuleInstalled('reader'))
{
	return true;
}

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/config.php');
$DB->Query("alter table `".READER_SUBSCRIPTION_ITEM_TABLE."` add column `date` integer not null after `name`");

?>