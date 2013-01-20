<?php

// 000033
// добавление свойства "скрыть в планировании" типам операций

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("insert into `".KASSA_OPERATION_PROPNAMES_TABLE."` (`name`, `type`, `code`)
	values ('Не показывать в планировании', 'checkbox', 'hideinplans')");

?>