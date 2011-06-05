<?php

/*
	Обработчик аяксовых запросов в модуле кассы.
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

switch($_REQUEST['mode'])
{
	case 'comment':
	{
		$comment = $DB->EscapeString($_REQUEST['comment']);
		if(strlen($comment) < 3)
		{
			return;
		}
		$tplengine = new CTemplateEngine('kassa');
		$res = $DB->Query("select `comment` from `".KASSA_OPERATION_TABLE."` where `comment` like '".$comment."%' group by `comment` order by count(`comment`) desc limit 15");
		$_comments = array();
		while($_row = $DB->Fetch($res))
		{
			$_comments[] = $_row['comment'];
		}
		$tplengine->assign('_comments', $_comments);
	}
	default:
	{
		break;
	}
}
if($tplengine)
{
	$tplengine->template('ajax_'.$_REQUEST['mode'].'.tpl');
}

?>