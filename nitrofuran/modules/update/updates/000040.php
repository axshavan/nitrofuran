<?php

// 000040
// добавить поле в таблицу ридера

// если модуль не установлен, ничего не делаем
if(!CModule::IsModuleInstalled('reader'))
{
	return true;
}

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/config.php');
$DB->Query("alter table `".READER_SUBSCRIPTION_ITEM_TABLE."` add column `text` text");

?>