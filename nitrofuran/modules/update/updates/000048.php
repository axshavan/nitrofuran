<?php

// 000048
// создание пары таблиц для модуля blog

if(!CModule::IsModuleInstalled('blog'))
{
	return true;
}
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/config.php');
$DB->TransactionStart();

// создание таблицы для хранения списка блогов
if(!$DB->Query("create table if not exists `".BLOG_TABLE."` (
	`id` integer unsigned auto_increment not null,
	`name` varchar(255) not null,
	`user_id` integer unsigned,
	`tree_id` integer unsigned,
	primary key(`id`)
) engine InnoDB"))
{
	$DB->TransactionRollback();
	return false;
}

// создание отдельной админской страницы управления блогами (/admin/blog)
$res = $DB->QueryFetched("select `id` from `".TREE_TABLE."` where `name` = 'admin' and `module` = 'admin'");
if($res[0] && $res[0]['id'])
{
	if(!$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
		('".(int)$res[0]['id']."', 'blog', 'blog', 'admin2', '', 0)"))
	{
		$DB->TransactionRollback();
		return false;
	}
}

$DB->TransactionCommit();
return true;

?>