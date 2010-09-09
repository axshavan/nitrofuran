<?php

/*
	Перенос денег со счёта на счёт.
*/

require_once('config.php');
global $DB;

if(!defined('OPTYPE_ACCOUNT_TRANSACTON_FROM_ID') || !defined('OPTYPE_ACCOUNT_TRANSACTON_TO_ID'))
{
	echo 'Ошибка. Требуемые константы не определены.';
	die();
}
$res       = $DB->Query("select `id`, `name` from `".KASSA_ACCOUNT_TABLE."` where `id` in (".(int)$_POST['account_from'].", ".(int)$_POST['account_to'].")");
$_accounts = array();
while($r = $DB->Fetch($res))
{
	$_accounts[$r['id']] = $r['name'];
}
$_POST['sum'] = str_replace(',', '.', $_POST['sum']);
$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`)
	values ('".(int)$_POST['currency']."', '".(int)$_POST['account_from']."', '".OPTYPE_ACCOUNT_TRANSACTON_FROM_ID."', '".(float)$_POST['sum']."', unix_timestamp(), 'На счёт ".$_accounts[$_POST['account_to']]."')");
$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`)
	values ('".(int)$_POST['currency']."', '".(int)$_POST['account_to']."', '".OPTYPE_ACCOUNT_TRANSACTON_TO_ID."', '".(float)$_POST['sum']."', unix_timestamp(), 'Со счёта ".$_accounts[$_POST['account_from']]."')");
redirect('..');

?>