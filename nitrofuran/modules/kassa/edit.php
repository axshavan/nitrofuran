<?php

/*
	Редактирование записи в кассе.
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