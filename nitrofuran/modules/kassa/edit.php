<?php

/*
	Редактирование записи в кассе.
*/

require_once('config.php');
global $DB;

if($_POST['id'] && $_POST['account'] && $_POST['optype'] && $_POST['currency'])
{
	$_POST['amount'] = str_replace(',', '.', $_POST['amount']);
	$DB->Query("update `".KASSA_OPERATION_TABLE."` set
		`currency_id` = '".(int)$_POST['currency']."',
		`account_id`  = '".(int)$_POST['account']."',
		`type_id`     = '".(int)$_POST['optype']."',
		`amount`      = '".(float)$_POST['amount']."',
		`comment`     = '".$DB->EscapeString($_POST['comment'])."',
		`backtime`    = '".mktime(0, 0, 0, $_POST['backmonth'], $_POST['backday'], $_POST['backyear'])."'
		where `id` = '".(int)$_POST['id']."'");
}
redirect('..');

?>