<?php

/*
	Безвозвратное удаление записи из кассы.
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

$id = (int)$_REQUEST['id'];
if($id)
{
	$DB->Query("delete from `".KASSA_OPERATION_TABLE."` where `id` = '".$id."'");
}
redirect($_SERVER['HTTP_REFERER']);

?>