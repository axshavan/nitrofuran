<?php

/*
	Обмен валюты.
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

$res = $DB->Query("select `name`, `value` from `".PARAMS_TABLE."` where `module` = 'kassa' and `name` in ('OPTYPE_CUREXCH_PLUS', 'OPTYPE_CUREXCH_MINUS')");
while($r = $DB->Fetch($res))
{
	${$r['name']} = $r['value'];
}
if(!$OPTYPE_CUREXCH_PLUS && !$OPTYPE_CUREXCH_MINUS)
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
$res         = $DB->Query("select `id`, `name` from `".KASSA_CURRENCY_TABLE."` where `id` in(".(int)$_POST['currency_from'].", ".(int)$_POST['currency_to'].")");
$_currencies = array();
while($r = $DB->Fetch($res))
{
	$_currencies[$r['id']] = $r['name'];
}
$sum_from = (float)str_replace(',', '.', $_POST['sum_from']);
$sum_to   = (float)str_replace(',', '.', $_POST['sum_to']);
$DB->Query("insert into `".KASSA_OPERATION_TABLE."`
	(`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`)
	values
	(
		'".(int)$_POST['currency_from']."',
		'".(int)$_POST['account_from']."',
		'".$OPTYPE_CUREXCH_MINUS."',
		'".$sum_from."',
		unix_timestamp(), 'В валюту ".$_currencies[$_POST['currency_to']]." на счёт ".$_accounts[$_POST['account_to']]."',
		unix_timestamp()
	)");
$DB->Query("insert into `".KASSA_OPERATION_TABLE."`
	(`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`)
	values
	(
		'".(int)$_POST['currency_to']."',
		'".(int)$_POST['account_to']."',
		'".$OPTYPE_CUREXCH_PLUS."',
		'".$sum_to."',
		unix_timestamp(), 'Из валюты ".$_currencies[$_POST['currency_from']]." со счёта ".$_accounts[$_POST['account_from']]."',
		unix_timestamp()
	)");
redirect('..');

?>