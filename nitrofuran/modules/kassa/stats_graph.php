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
	$bWeekAverage = (bool)$_REQUEST['weekaverage'];
	
	// выборка tipov операций
	$_optypes_by_id = array();
	$res = $DB->Query("select `id`, `is_income` from `".KASSA_OPERATION_TYPE_TABLE."`");
	while($_row = $DB->Fetch($res))
	{
		$_optypes_by_id[$_row['id']] = $_row['is_income'];
	}
	
	// выборка операций
	$filter_from = (int)$_REQUEST['from'];
	$filter_to   = (int)$_REQUEST['to'];
	if($filter_from >= $filter_to)
	{
		die();
	}
	$sql_str = "select `amount`, `backtime`, `type_id`
		from `".KASSA_OPERATION_TABLE."` where
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
	$_op_by_date_i = array();
	$_op_by_date_e = array();
	$res           = $DB->Query($sql_str);
	while($_row = $DB->Fetch($res))
	{
		if($_optypes_by_id[$_row['type_id']])
		{
			$_op_by_date_i[date('Y-m-d', $_row['backtime'])][] = (float)$_row['amount'];
			$_op_by_date_e[date('Y-m-d', $_row['backtime'])][] = 0.0;
		}
		else
		{
			$_op_by_date_e[date('Y-m-d', $_row['backtime'])][] = (float)$_row['amount'];
			$_op_by_date_i[date('Y-m-d', $_row['backtime'])][] = 0.0;
		}
	}
	
	// нахождение суммарных значений для каждой даты
	$_sum_by_date_i = array();
	$_sum_by_date_e = array();
	$_avg_by_week_i = array();
	$_avg_by_week_e = array();
	$iwd            = array();
	$bDataExists    = false;
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
		$isize = sizeof($_op_by_date_i[$idate]);
		if($isize)
		{
			$_sum_by_date_i[$idate] = array_sum($_op_by_date_i[$idate]);
			$_sum_by_date_e[$idate] = array_sum($_op_by_date_e[$idate]);
			if($bWeekAverage)
			{
				if($iwd)
				{
					for($wd = 0; $wd < 7; $wd++)
					{
						$_avg_by_week_i[$iwd[$wd]] += $_sum_by_date_i[$idate];
						$_avg_by_week_e[$iwd[$wd]] += $_sum_by_date_e[$idate];
					}
				}
				else
				{
					$_avg_by_week_i[$idate] = $_sum_by_date_i[$idate];
					$_avg_by_week_e[$idate] = $_sum_by_date_e[$idate];
				}
			}
			$bDataExists = true;
		}
		else
		{
			$_sum_by_date_i[$idate] = 0;
			$_sum_by_date_e[$idate] = 0;
			if($bWeekAverage)
			{
				if($iwd)
				{
					for($wd = 0; $wd < 7; $wd++)
					{
						$_avg_by_week_i[$iwd[$wd]] += 0;
						$_avg_by_week_e[$iwd[$wd]] += 0;
					}
				}
				else
				{
					$_avg_by_week_i[$idate] = 0;
					$_avg_by_week_e[$idate] = 0;
				}
			}
		}
	}
	unset($_op_by_date_i);
	unset($_op_by_date_e);
	
	// усреднение по неделям
	foreach($_avg_by_week_i as &$v)
	{
		$v /= 7;
	}
	foreach($_avg_by_week_e as &$v)
	{
		$v /= 7;
	}
	
	// сама картинка
	if(sizeof($_sum_by_date_i) && $bDataExists)
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
					'00a000',
					'004000',
					'ff0000',
					'800000'
				),
				'xtick' => ceil(sizeof($_sum_by_date_i) / 20)
			),
			array
			(
				$_sum_by_date_i,
				$bWeekAverage ? $_avg_by_week_i : array(),
				$_sum_by_date_e,
				$bWeekAverage ? $_avg_by_week_e : array(),
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