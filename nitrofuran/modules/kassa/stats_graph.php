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
	$_op_by_date  = array();
	$_avg_by_week = array();
	$res          = $DB->Query($sql_str);
	while($_row = $DB->Fetch($res))
	{
		$_op_by_date[date('Y-m-d', $_row['backtime'])][] = $_row['amount'];
		//$_avg_by_week[date('Y-j', $_row['backtime'])][] = $_row['amount'];
	}
	/*foreach($_avg_by_week as &$v)
	{
		if(sizeof($v))
		{
			$v = array_sum($v) / sizeof($v);
		}
		else
		{
			$v = 0;
		}
	}*/
	//print_r($_avg_by_week); die();
	
	// нахождение суммарных значений для каждой даты
	$_sum_by_date = array();
	$iwd          = array();
	$bDataExists  = false;
	for($i = $filter_from; $i <= $filter_to; $i += 86400)
	{
		$idate = date('Y-m-d', $i);
		if(!date('w', $i))
		{
			for($wd = 0; $wd < 7; $wd++)
			{
				$iwd[$wd] = date('Y-m-d', $i + 86400 * $wd);
			}
		}
		$isize = sizeof($_op_by_date[$idate]);
		if($isize)
		{
			$_sum_by_date[$idate] = array_sum($_op_by_date[$idate]);
			if($iwd)
			{
				for($wd = 0; $wd < 7; $wd++)
				{
					$_avg_by_week[$iwd[$wd]] += $_sum_by_date[$idate];
				}
			}
			else
			{
				$_avg_by_week[$idate] = $_sum_by_date[$idate];
			}
			$bDataExists = true;
		}
		else
		{
			$_sum_by_date[$idate] = 0;
			if($iwd)
			{
				for($wd = 0; $wd < 7; $wd++)
				{
					$_avg_by_week[$iwd[$wd]] += 0;
				}
			}
			else
			{
				$_avg_by_week[$idate] = 0;
			}
		}
	}
	unset($_op_by_date);
	
	// усреднение по неделям
	foreach($_avg_by_week as &$v)
	{
		$v /= 7;
	}
	
	// сама картинка
	if(sizeof($_sum_by_date) && $bDataExists)
	{
		header("Content-Type: image/png");
		imagepng(CChart::multiline_graph
		(
			array
			(
				'width' => 1000,
				'height' => 500,
				'colors' => array
				(
					'ff0000',
					'00ff00'
				),
				'xtick' => ceil(sizeof($_sum_by_date) / 20)
			),
			array
			(
				$_sum_by_date,
				$_avg_by_week
			)
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
	$tplengine = new CTemplateEngine('kassa');
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
	$weekaverage = $_POST['weekaverage'] ? 1 : 0;
	$tplengine->assign('filter_from', date('Y-m-d', $filter_from));
	$tplengine->assign('filter_to',   date('Y-m-d', $filter_to));
	$tplengine->assign('weekaverage', $weekaverage);
	
	if($_REQUEST['draw'])
	{
		$image_src = '?image'
			.'&comment='.  h($_REQUEST['comment'])
			.'&currency='.(int)($_REQUEST['currency'])
			.'&account='. (int)($_REQUEST['account'])
			.'&optype='.  (int)($_REQUEST['optype'])
			.'&from='.    $filter_from
			.'&to='.      $filter_to
			.($weekaverage ? '&weekaverage=1' : '');
		$tplengine->assign('image_src', $image_src);
	}
	
	$tplengine->template('stats_graph.tpl');
}

?>