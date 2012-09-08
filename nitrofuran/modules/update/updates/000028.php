<?php

// 000028
// добавление раздела для списка операций второй версии в кассе

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
$DB->TransactionStart();

$res = $DB->QueryFetched("select `id`, `access` from `".TREE_TABLE."` where
	`module`     = 'kassa'
	and `action` = ''
	and `name`   = 'kassa'");
$kassa_root_id     = $res[0]['id'];
$kassa_root_access = $res[0]['access'];
if(!$DB->Query("update `".TREE_TABLE."` set `template` = 'optable.tpl' where `id` = '".$kassa_root_id."'"))
{
	$DB->TransactionRollback();
	return false;
}
if(!$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	(
		'".$kassa_root_id."',
		'v2',
		'kassa',
		'index',
		'optable_v2.tpl',
		'".$kassa_root_access."'
	)"))
{
	$DB->TransactionRollback();
	return false;
}

$DB->TransactionCommit();
return true;

?>