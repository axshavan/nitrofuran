<?php

/*
	Отложенные суммы в кассе.
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

if($_GET['del'])
{
	// удаление записи
	$DB->Query("delete from `".KASSA_HOLD_TABLE."` where `id` = '".(int)$_GET['del']."'");
	redirect($_SERVER['HTTP_REFERER']);
	die();
}
if($_GET['done'])
{
	// перенос записи из холда в операции
	$row = $DB->QueryFetched("select * from `".KASSA_HOLD_TABLE."` where `id` = '".(int)$_GET['done']."'");
	if($row[0])
	{
		$row = $row[0];
		$DB->Query("insert into `".KASSA_OPERATION_TABLE."`
			(
				`currency_id`,
				`account_id`,
				`type_id`,
				`amount`,
				`time`,
				`comment`,
				`backtime`
			)
			values
			(
				'".(int)$row['currency_id']."',
				'".(int)$row['account_id']."',
				'".(int)$row['operation_type_id']."',
				'".(float)$row['sum']."',
				unix_timestamp(),
				'".$row['comment']."',
				unix_timestamp()
			)
		");
		$DB->Query("delete from `".KASSA_HOLD_TABLE."` where `id` = '".$row['id']."'");
	}
	redirect($_SERVER['HTTP_REFERER']);
	die();
}

if($_POST)
{
	if(!$_POST['id'])
	{
		// добавление записи
		$DB->Query("insert into `".KASSA_HOLD_TABLE."` (`operation_type_id`, `sum`, `comment`, `currency_id`, `account_id`) values (
			'".(int)$_POST['optype']."',
			'".(float)$_POST['amount']."',
			'".$DB->EscapeString($_POST['comment'])."',
			'".(int)$_POST['currency']."',
			'".(int)$_POST['account']."')");
	}
	else
	{
		// редактирование записи
		$DB->Query("update `".KASSA_HOLD_TABLE."` set
			`operation_type_id` = '".(int)$_POST['optype']."',
			`sum` = '".(float)$_POST['amount']."',
			`comment` = '".$DB->EscapeString($_POST['comment'])."',
			`currency_id` = '".(int)$_POST['currency']."',
			`account_id` = '".(int)$_POST['account']."'
			where `id` = '".(int)$_POST['id']."'");
	}
}
redirect($_SERVER['HTTP_REFERER']);

?>