<?php

// 000038
// изменение таблиц модуля reader

// если модуль не установлен, ничего не делаем
if(!CModule::IsModuleInstalled('reader'))
{
	return true;
}

// изменение таблиц
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/config.php');
$DB->Query("alter table `".READER_SUBSCRIPTION_TABLE."` add column `href` text after `name`, add column `sort` integer unsigned not null default 0");
$DB->Query("alter table `".READER_SUBSCRIPTION_GROUP_TABLE."` add column `sort` integer unsigned not null default 0");

?>