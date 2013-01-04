<?php

// 000032
// добавление страницы API в кассу

if(!CModule::IsModuleInstalled('kassa'))
{
	// дранкипер не установлен
	return true;
}

global $DB;
$DB->TransactionStart();

$res = $DB->QueryFetched("select `id` from `".TREE_TABLE."` where
	`module`     = 'kassa'
	and `action` = ''
	and `name`   = 'kassa'");
$root_id     = $res[0]['id'];
if(!$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	(
		'".$root_id."',
		'api',
		'kassa',
		'api',
		'',
		'1'
	)"))
{
	$DB->TransactionRollback();
	return false;
}

$DB->TransactionCommit();
return true;

?>