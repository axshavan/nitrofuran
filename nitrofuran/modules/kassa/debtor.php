<?php

/*
	Обработка операций с долгами в кассе.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
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
	$_REQUEST['debtor_amount'] = str_replace(',', '.', $_REQUEST['debtor_amount']);
	if($_REQUEST['debtor_operation'] == 2)
	{
		// просто измнение суммы долга
		$DB->Query("insert into `".KASSA_DEBTORS_OPERATION_TABLE."`
			(`debtor_id`, `date`, `amount`, `currency_id`)
			values ('".$_REQUEST['debtor_id']."', unix_timestamp(), '".(float)$_REQUEST['debtor_amount']."', '".(int)$_REQUEST['debtor_currency']."')");
	}
	else
	{
		// обычное добавление операции
		$DB->TransactionStart();
		$amount      = (float)$_REQUEST['debtor_amount'] * ($_REQUEST['debtor_operation'] ? 1 : -1);
		$res         = $DB->Query("select `name` from `".KASSA_DEBTORS_TABLE."` where `id` = '".$_REQUEST['debtor_id']."'");
		$debtor_name = $DB->Fetch($res);
		$debtor_name = $debtor_name['name'];
		$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`) values
			('".(int)$_REQUEST['debtor_currency']."', 1, '".$OPTYPE_DEBTOR."', '".($amount * ($amount > 0 ? 1 : -1))."', unix_timestamp(), '(".$debtor_name.") ".$DB->EscapeString($_REQUEST['debtor_comment'])."', unix_timestamp())");
		$operation_id = $DB->InsertedId();
		if(!$operation_id)
		{
			echo $DB->Error();
			$DB->TransactionRollback();
			die();
		}
		$DB->Query("insert into `".KASSA_DEBTORS_OPERATION_TABLE."`
			(`debtor_id`, `date`, `amount`, `currency_id`, `operation_id`)
			values ('".$_REQUEST['debtor_id']."', unix_timestamp(), '".$amount."', '".(int)$_REQUEST['debtor_currency']."', '".$operation_id."')");
		$DB->TransactionCommit();
	}
}
redirect('..');

?>