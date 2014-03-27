<?php

// 000049
// создание таблицы с постами блогов
if(!CModule::IsModuleInstalled('blog'))
{
	return true;
}

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/config.php');
$DB->TransactionStart();

// создание таблицы для хранения списка постов
if(!$DB->Query("create table if not exists `".BLOG_POST_TABLE."` (
	`id` integer unsigned auto_increment not null,
	`blog_id` integer unsigned,
	`title` varchar(255),
	`text` text,
	`date_create` integer unsigned not null,
	primary key(`id`)
) engine InnoDB"))
{
	$DB->TransactionRollback();
	return false;
}
$DB->TransactionCommit();
return true;

?>

?>