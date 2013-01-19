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

if($_POST['amount'] && $_POST['account'] && $_POST['optype'] && $_POST['currency'])
{
	require_once('config.php');
	require_once(dirname(__FILE__).'/kassa.php');
	$kassa = new CKassa();

	if($_POST['backyear'] && $_POST['backmonth'] && $_POST['backday'])
	{
		$backtime = mktime(0, 0, 0, $_POST['backmonth'], $_POST['backday'], $_POST['backyear']);
	}
	else
	{
		$backtime = false;
	}
	if(!$kassa->Add
	(
		array
		(
			'amount'   => $_POST['amount'],
			'optype'   => $_POST['optype'],
			'currency' => $_POST['currency'],
			'account'  => $_POST['account'],
			'comment'  => $_POST['comment'],
			'backtime' => $backtime
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