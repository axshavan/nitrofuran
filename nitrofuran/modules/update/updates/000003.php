<?php

// 000003
// страница с планированием бюджета на месяц в кассе

global $DB;
$DB->TransactionStart();

// добавление странички с планами
$res  = $DB->Query("select `id` from `".TREE_TABLE."` where `action` = '' and `module` = 'kassa'");
$_row = $DB->Fetch($res);
if(!$_row['id'])
{
	$DB->TransactionRollback();
	return false;
}
$res = $DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$_row['id']."', 'plans', 'kassa', 'plans', '', 1)");
if($res === false)
{
	$DB->TransactionRollback();
	return false;
}

new_param('kassa', 'plans_title', 'Что наверху написано в планировании', 'text', 'Планирование');

$DB->TransactionCommit();

?>