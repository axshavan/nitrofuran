<?php

// 000036
// для единообразия изменение движка таблицы в кассе

// если модуль не установлен, ничего не делаем
if(!CModule::IsModuleInstalled('kassa'))
{
	return true;
}
// добавление таблицы
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("alter table `".KASSA_HOLD_TABLE."` engine=InnoDB");
return true;

?>