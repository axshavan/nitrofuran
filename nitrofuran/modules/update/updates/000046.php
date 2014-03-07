<?php

// 000046
// убрать отдельную таблицу для списка книг в модуле марок

if(!CModule::IsModuleInstalled('stamps'))
{
	// модуль марок не установлен
	return true;
}
$DB->Query("drop table if exists `stamps_books`");

?>