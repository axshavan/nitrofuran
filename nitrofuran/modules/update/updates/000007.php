<?php

// 000007
// добавление свойства "считать в планировании" типам операций в кассе

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("insert into `".KASSA_OPERATION_PROPNAMES_TABLE."` (`name`, `type`) values ('Считать в планировании', 'checkbox')");

?>