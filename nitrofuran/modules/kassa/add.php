<?php

/*
	Добавление записи в кассу.
*/

require_once('config.php');
global $DB;

if($_POST['amount'] && $_POST['account'] && $_POST['optype'] && $_POST['currency'])
{
	$_POST['amount'] = str_replace(',', '.', $_POST['amount']);
	$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`)
		values ('".(int)$_POST['currency']."', '".(int)$_POST['account']."', '".(int)$_POST['optype']."', '".(float)$_POST['amount']."', unix_timestamp(), '".$DB->EscapeString($_POST['comment'])."')");
}
redirect('..');

?>