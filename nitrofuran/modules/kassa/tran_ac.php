<?php

/*
	Перенос денег со счёта на счёт.
*/

require_once('config.php');
global $DB;

$res = $DB->Query("select `name`, `value` from `".PARAMS_TABLE."` where `module` = 'kassa' and `name` in ('OPTYPE_TRANSACTION_FROM_ID', 'OPTYPE_TRANSACTION_TO_ID', 'OPTYPE_TRANSACTION_COMISSION_ID')");
while($r = $DB->Fetch($res))
{
	${$r['name']} = $r['value'];
}
if(!$OPTYPE_TRANSACTION_TO_ID && !$OPTYPE_TRANSACTION_FROM_ID)
{
	echo 'Ошибка. Не определены необходимые номера типов операций.';
	die();
}
$res       = $DB->Query("select `id`, `name` from `".KASSA_ACCOUNT_TABLE."` where `id` in (".(int)$_POST['account_from'].", ".(int)$_POST['account_to'].")");
$_accounts = array();
while($r = $DB->Fetch($res))
{
	$_accounts[$r['id']] = $r['name'];
}
$sum_from = $sum_to = (float)str_replace(',', '.', $_POST['sum']);
if($OPTYPE_TRANSACTION_COMISSION_ID)
{
	$sum_comission = ($_POST['comission'] / 100) * $sum_from;
}
$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`)
	values ('".(int)$_POST['currency']."', '".(int)$_POST['account_from']."', '".$OPTYPE_TRANSACTION_FROM_ID."', '".$sum_from."', unix_timestamp(), 'На счёт ".$_accounts[$_POST['account_to']]."',    unix_timestamp())");
$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`)
	values ('".(int)$_POST['currency']."', '".(int)$_POST['account_to']."',   '".$OPTYPE_TRANSACTION_TO_ID."',   '".$sum_to."',   unix_timestamp(), 'Со счёта ".$_accounts[$_POST['account_from']]."', unix_timestamp())");
if($OPTYPE_TRANSACTION_COMISSION_ID && $sum_comission)
{
	$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`)
		values ('".(int)$_POST['currency']."', '".(int)$_POST['account_to']."', '".$OPTYPE_TRANSACTION_COMISSION_ID."', '".$sum_comission."', unix_timestamp(), 'С переноса со счёта ".$_accounts[$_POST['account_from']]."', unix_timestamp())");
}
redirect('..');

?>