<?php

// 000035
// добавление таблицы для метаданных статичного модуля

// если модуль не установлен, ничего не делаем
if(!CModule::IsModuleInstalled('static'))
{
	return true;
}
// добавление таблицы
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/static/config.php');
$DB->Query("CREATE TABLE if not exists `".STATIC_META_TABLE."` (
	`id` integer  NOT NULL AUTO_INCREMENT,
	`page_id` integer,
	`meta_key` varchar(255),
	`content` text  NOT NULL,
	PRIMARY KEY (`id`)
  )
  ENGINE = InnoDB");
return true;

?>