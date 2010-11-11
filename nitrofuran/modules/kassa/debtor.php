<?php

/*
	Обработка операций с долгами в кассе.
*/

require_once('config.php');
global $DB;

if($_REQUEST['debtor_id'])
{
	$_REQUEST['debtor_id'] = (int)$_REQUEST['debtor_id'];
	if(!$_REQUEST['debtor_id'])
	{
		echo 'Ошибка. Неверные параметры.';
		die();
	}
	$OPTYPE_DEBTOR = get_param('kassa', 'OPTYPE_DEBTOR_'.($_REQUEST['debtor_operation'] ? 'CREDIT' : 'DEBIT'));
	if(!$OPTYPE_DEBTOR)
	{
		echo 'Ошибка. Не определён тип операций.';
		die();
	}
	$amount      = (float)$_REQUEST['debtor_amount'] * ($_REQUEST['debtor_operation'] ? 1 : -1);
	$res         = $DB->Query("select `name` from `".KASSA_DEBTORS_TABLE."` where `id` = '".$_REQUEST['debtor_id']."'");
	$debtor_name = $DB->Fetch($res);
	$debtor_name = $debtor_name['name'];
	$DB->Query("insert into `".KASSA_DEBTORS_OPERATION_TABLE."`
		(`debtor_id`, `date`, `amount`, `currency_id`)
		values ('".$_REQUEST['debtor_id']."', unix_timestamp(), '".$amount."', '".(int)$_REQUEST['debtor_currency']."')");
	$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`) values
		('".(int)$_REQUEST['debtor_currency']."', 1, '".$OPTYPE_DEBTOR."', '".($amount * ($amount > 0 ? 1 : -1))."', unix_timestamp(), '(".$debtor_name.") ".$DB->EscapeString($_REQUEST['debtor_comment'])."')");
}
redirect('..');

?>