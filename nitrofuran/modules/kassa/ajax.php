<?php

/*
	Обработчик аяксовых запросов в модуле кассы.
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
		$tplengine = new template_engine('kassa');
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