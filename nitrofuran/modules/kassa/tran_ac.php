<?php

/*
	Перенос денег со счёта на счёт.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require_once('config.php');
require_once(dirname(__FILE__).'/kassa.php');
global $DB;
$kassa = new CKassa();

$OPTYPE_TRANSACTION_FROM_ID      = get_param('kassa', 'OPTYPE_TRANSACTION_FROM_ID');
$OPTYPE_TRANSACTION_TO_ID        = get_param('kassa', 'OPTYPE_TRANSACTION_TO_ID');
$OPTYPE_TRANSACTION_COMISSION_ID = get_param('kassa', 'OPTYPE_TRANSACTION_COMISSION_ID');
if(!$OPTYPE_TRANSACTION_TO_ID && !$OPTYPE_TRANSACTION_FROM_ID)
{
	die('Ошибка: не определены необходимые номера типов операций');
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
	$sum_comission = ((float)str_replace(',', '.', $_POST['comission']) / 100) * $sum_from;
}

if(!$kassa->Add
(
	array(
		'amount'   => $sum_from,
		'optype'   => $OPTYPE_TRANSACTION_FROM_ID,
		'currency' => $_POST['currency'],
		'account'  => $_POST['account_from'],
		'comment'  => 'На счёт '.$_accounts[$_POST['account_to']]
	),
	$error_code,
	$error_message
))
{
	die('Ошибка: '.$error_message);
}

if(!$kassa->Add
(
	array(
		'amount'   => $sum_to,
		'optype'   => $OPTYPE_TRANSACTION_TO_ID,
		'currency' => $_POST['currency'],
		'account'  => $_POST['account_to'],
		'comment'  => 'Со счёта '.$_accounts[$_POST['account_from']]
	),
	$error_code,
	$error_message
))
{
	die('Ошибка: '.$error_message);
}

if($OPTYPE_TRANSACTION_COMISSION_ID && $sum_comission)
{
	if(!$kassa->Add
	(
		array(
			'amount'   => $sum_comission,
			'optype'   => $OPTYPE_TRANSACTION_COMISSION_ID,
			'currency' => $_POST['currency'],
			'account'  => $_POST['account_to'],
			'comment'  => 'С переноса со счёта '.$_accounts[$_POST['account_from']]
		),
		$error_code,
		$error_message
	))
	{
		die('Ошибка: '.$error_message);
	}
}
redirect($_SERVER['HTTP_REFERER']);

?>