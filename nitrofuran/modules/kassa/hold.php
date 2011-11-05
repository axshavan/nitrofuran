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

//trace($_POST);

if(!$_POST['id'])
{
	// добавление записи
	$DB->Query("insert into `".KASSA_HOLD_TABLE."` (`operation_type_id`, `sum`, `comment`) values
		('".(int)$_POST['optype']."', '".(float)$_POST['amount']."', '".$DB->EscapeString($_POST['comment'])."')");
}
else
{
	// редактирование записи
	$DB->Query("update `".KASSA_HOLD_TABLE."` set
		`operation_type_id` = '".(int)$_POST['optype']."',
		`sum` = '".(float)$_POST['amount']."',
		`comment` = '".$DB->EscapeString($_POST['comment'])."'
		where `id` = '".(int)$_POST['id']."'");
}

redirect('..');

?>