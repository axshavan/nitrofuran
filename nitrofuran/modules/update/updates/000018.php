<?php

// 000018
// добавление параметра "использовать мобильные шаблоны" в кассу

$DB->Query("alter table `".PARAMS_TABLE."` modify column `type` enum('text','textarray','textarea','checkbox') NOT NULL DEFAULT 'text'");

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

return new_param
(
	'kassa',
	'use_mobile_templates',
	'Использовать мобильные шаблоны',
	'checkbox',
	'1'
);

?>