<?php

// 000029
// добавление генератора картинок в drunkeeper

if(!CModule::IsModuleInstalled('drunkeeper'))
{
	// дранкипер не установлен
	return true;
}

global $DB;
$DB->TransactionStart();

$res = $DB->QueryFetched("select `id`, `access` from `".TREE_TABLE."` where
	`module`     = 'drunkeeper'
	and `action` = ''
	and `name`   = 'drunkeeper'");
$root_id     = $res[0]['id'];
$root_access = $res[0]['access'];
if(!$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	(
		'".$root_id."',
		'plot',
		'drunkeeper',
		'plot',
		'',
		'".$root_access."'
	)"))
{
	$DB->TransactionRollback();
	return false;
}

$DB->TransactionCommit();
return true;

?>

?>