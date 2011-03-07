<?php

/*
	Статистика с графиками.
*/

require_once('config.php');
global $DB;

if(isset($_REQUEST['image']))
{
	// генерация картинки
	require_once(DOCUMENT_ROOT.'/nitrofuran/chart.class.php');
	
	// выборка операций
	$filter_from = (int)$_REQUEST['from'];
	$filter_to   = (int)$_REQUEST['to'];
	if($filter_from >= $filter_to)
	{
		die();
	}
	$sql_str = "select `amount`, `backtime` from `".KASSA_OPERATION_TABLE."` where
		`time` >= '".$filter_from."'
		and `time` <= '".$filter_to."'";
	if($_REQUEST['comment'])
	{
		$sql_str .= " and `comment` = '".$DB->EscapeString($_REQUEST['comment'])."'";
	}
	if($_REQUEST['currency'])
	{
		$sql_str .= " and `currency_id` = '".(int)$_REQUEST['currency']."'";
	}
	if($_REQUEST['account'])
	{
		$sql_str .= " and `account_id` = '".(int)$_REQUEST['account']."'";
	}
	if($_REQUEST['optype'])
	{
		$sql_str .= " and `type_id` = '".(int)$_REQUEST['optype']."'";
	}
	$_op_by_date = array();
	$res         = $DB->Query($sql_str);
	while($_row = $DB->Fetch($res))
	{
		$_op_by_date[date('Y-m-d', $_row['backtime'])][] = $_row['amount'];
	}
	
	// нахождение средних значений для каждой даты
	$_median_by_date = array();
	$bDataExists = false;
	for($i = $filter_from; $i <= $filter_to; $i += 86400)
	{
		$idate = date('Y-m-d', $i);
		$isize  = sizeof($_op_by_date[$idate]);
		if($isize)
		{
			$_median_by_date[$idate] = array_sum($_op_by_date[$idate]) / $isize;
			$bDataExists = true;
		}
		else
		{
			$_median_by_date[$idate] = 0;
		}
	}
	unset($_op_by_date);
	
	// сама картинка
	if(sizeof($_median_by_date) && $bDataExists)
	{
		imagepng(chart::multiline_graph
		(
			array
			(
				'width' => 1000,
				'height' => 500,
				'colors' => array
				(
					'ff0000'
				),
				'xtick' => ceil(sizeof($_median_by_date) / 20)
			),
			array($_median_by_date)
		));
	}
	else
	{
		echo file_get_contents(DOCUMENT_ROOT.'/i/kassa/nodata2show.png');
	}
	die();
}
else
{
	// генерация собственно страницы
	$tplengine = new template_engine('kassa');
	$tplengine->assign('title', get_param('kassa', 'stats_title'));
	
	// группы типов операций
	$_optypegroups = array();
	$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_GROUP_TABLE."`");
	while($_row = $DB->Fetch($res))
	{
		$_optypegroups[$_row['id']] = $_row;
	}
	$tplengine->assign('_optypegroups', $_optypegroups);
	
	// типы операций
	$_optypes       = array();
	$_optypes_by_id = array();
	$group          = 0;
	$res            = $DB->Query("select * from `".KASSA_OPERATION_TYPE_TABLE."` order by `group_id` asc");
	while($_row = $DB->Fetch($res))
	{
		if($group != $_row['group_id'])
		{
			$group = $_row['group_id'];
			$_optypes[$group] = array();
		}
		$_optypes[$group][] = $_row;
		$_optypes_by_id[$_row['id']] = $_row;
		if($_row['id'] == $filter_type)
		{
			$tplengine->assign('show_group', $group);
		}
	}
	$tplengine->assign('_optypes',       $_optypes);
	$tplengine->assign('_optypes_by_id', $_optypes_by_id);
	
	// валюты
	$_currencies = array();
	$res = $DB->Query("select * from `".KASSA_CURRENCY_TABLE."`");
	while($_row = $DB->Fetch($res))
	{
		$_currencies[$_row['id']] = $_row;
	}
	$tplengine->assign('_currencies', $_currencies);
	
	// счета
	$_accounts = array();
	$res = $DB->Query("select * from `".KASSA_ACCOUNT_TABLE."`");
	while($_row = $DB->Fetch($res))
	{
		$_accounts[] = $_row;
	}
	$tplengine->assign('_accounts', $_accounts);
	
	// поехали
	$filter_from = strtotime($_REQUEST['filter_from']);
	if(!$filter_from)
	{
		$filter_from = time() - 86400 * 7;
	}
	$filter_to = strtotime($_REQUEST['filter_to']);
	if(!$filter_to)
	{
		$filter_to = time();
	}
	$tplengine->assign('filter_from', date('Y-m-d', $filter_from));
	$tplengine->assign('filter_to',   date('Y-m-d', $filter_to));
	
	if($_REQUEST['draw'])
	{
		$image_src = '?image'
			.'&comment='.  h($_REQUEST['comment'])
			.'&currency='.(int)($_REQUEST['currency'])
			.'&account='. (int)($_REQUEST['account'])
			.'&optype='.  (int)($_REQUEST['optype'])
			.'&from='.    $filter_from
			.'&to='.      $filter_to;
		$tplengine->assign('image_src', $image_src);
	}
	
	$tplengine->template('stats_graph.tpl');
}

?>