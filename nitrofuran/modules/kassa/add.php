<?php

/*
	Добавление записи в кассу.
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

if($_POST['amount'] && $_POST['account'] && $_POST['optype'] && $_POST['currency'])
{
	$_POST['amount'] = str_replace(',', '.', $_POST['amount']);
	if($_POST['backyear'] && $_POST['backmonth'] && $_POST['backday'])
	{
		$backtime = mktime(0, 0, 0, $_POST['backmonth'], $_POST['backday'], $_POST['backyear']);
	}
	else
	{
		$backtime = time();
	}
	$DB->Query("insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`, `amount`, `time`, `comment`, `backtime`)
		values ('".(int)$_POST['currency']."', '".(int)$_POST['account']."', '".(int)$_POST['optype']."', '".(float)$_POST['amount']."', unix_timestamp(), '".$DB->EscapeString($_POST['comment'])."', '".$backtime."')");
}
redirect($_SERVER['HTTP_REFERER']);

?>