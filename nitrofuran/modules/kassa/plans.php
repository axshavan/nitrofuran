<?php

/*
	Страница кассы с планированием бюджета на месяц.
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

if($_REQUEST['switch'])
{
	$switch_id = (int)$_REQUEST['switch'];
	if($switch_id)
	{
		$res = $DB->Query("select `id` from `".KASSA_OPERATION_PROPNAMES_TABLE."` where `code` = 'showinplans'");
		$option_id = $DB->Fetch($res);
		$option_id = $option_id['id'];
		$DB->Query("update `".KASSA_OPERATION_PROPVALUES_TABLE."` set `value` = if(`value` = 1, 0, 1) where `operation_type_id` = '".$switch_id."' and `option_id` = '".$option_id."'");
		if(!$DB->AffectedRows())
		{
			$DB->Query("insert into `".KASSA_OPERATION_PROPVALUES_TABLE."` (`operation_type_id`, `option_id`, `value`) values ('".$switch_id."', '".$option_id."', '1')");
		}
		redirect('.');
	}
}

$_opbytype   = array();
$_sumbyacc   = array();
$_currencies = array();
$_optypes    = array();
$_sumbycur   = array();

// сколько осталось от текущего месяца
$mprogress = 1 - (date('j') - 1) / date('t');

// валюты
$res = $DB->Query("select * from `".KASSA_CURRENCY_TABLE."`");
while($_row = $DB->Fetch($res))
{
	$_currencies[$_row['id']] = $_row;
}

// типы операций
$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_TABLE."`");
while($_row = $DB->Fetch($res))
{
	$_optypes[$_row['id']] = $_row;
}

// операции
$op_res = $DB->Query("select `currency_id`, `account_id`, `type_id`, `amount`, `time`, `backtime` from `".KASSA_OPERATION_TABLE."`");
while($_op = $DB->Fetch($op_res))
{
	$amount = $_op['amount'] * ($_optypes[$_op['type_id']]['is_income'] ? 1 : -1);
	$_opbytype[$_op['type_id']][$_op['currency_id']]['sum'] += $amount;
	$_opbytype[$_op['type_id']][$_op['currency_id']]['count']++;
	$_opbytype[$_op['type_id']][$_op['currency_id']]['last_time'] = $_op['backtime'];
	if(!$_opbytype[$_op['type_id']][$_op['currency_id']]['first_time'])
	{
		$_opbytype[$_op['type_id']][$_op['currency_id']]['first_time'] = $_op['backtime'];
	}
	$_sumbyacc[$_op['account_id']][$_op['currency_id']] += $amount;
	$_sumbycur[$_op['currency_id']]                     += $amount;
}

// итоговая сумма по валютам
$_result_sum_by_cur = array();

// достаём свойство "считаем в планировании" операций
$_count_in_plans = array();
$res = $DB->Query("select pv.* from `".KASSA_OPERATION_PROPVALUES_TABLE."` pv, `".KASSA_OPERATION_PROPNAMES_TABLE."` pn where pn.`code` = 'showinplans' and pn.`id` = pv.`option_id`");
while($_row = $DB->Fetch($res))
{
	if($_row['value'])
	{
		$_count_in_plans[] = $_row['operation_type_id'];
	}
}

// постобработка операций
foreach($_opbytype as $optype_id => &$_op)
{
	foreach($_currencies as $c => $v)
	{
		$m1 = date('Y', $_op[$c]['first_time']) * 12 + date('m', $_op[$c]['first_time']);
		$m2 = date('Y', $_op[$c]['last_time']) * 12  + date('m', $_op[$c]['last_time']);
		$_op[$c]['months']    = $m2 - $m1 + 1;
		$_op[$c]['average_c'] = round($_op[$c]['sum'] / $_op[$c]['count'], 2);
		$_op[$c]['average_m'] = $_op[$c]['sum'] / $_op[$c]['months'];
		$_op[$c]['left_m']    = round($_op[$c]['average_m'] * $mprogress, 2);
		$_op[$c]['average_m'] = round($_op[$c]['average_m'], 2);
		if(in_array($optype_id, $_count_in_plans))
		{
			if($_op[$c]['last_time'] < time() - 5184000) // последний раз ранее, чем 60 суток назад
			{
				$_op[$c]['do_not_count'] = 'ancient';
			}
			elseif($_optypes[$optype_id]['is_service'])
			{
				$_op[$c]['do_not_count'] = 'service';
			}
			else
			{
				$_result_sum_by_cur[$c] += $_op[$c]['left_m'];
			}
		}
		else
		{
			$_op[$c]['do_not_count'] = 'disabled';
		}
		if(!$_op[$c]['average_c'])
		{
			unset($_op[$c]);
		}
	}
}

// планируемые операции
$_plans = array();
$res = $DB->Query("select * from `".KASSA_PLANS_TABLE."` where `active`");
while($_row = $DB->Fetch($res))
{
	switch($_row['repeat_type'])
	{
		case 'daily':
		{
			$_row['repeat'] = strtotime($_row['repeat']);
			$start_time = mktime(0,   0,  0, date('n'), date('d'), date('Y'));
			$end_time   = mktime(23, 59, 59, date('n'), date('t'), date('Y'));
			for($time = $start_time; $time < $end_time; $time += 86400)
			{
				$_row['repeat'] = date('Y-m-d', $time);
				$_plans[] = $_row;
			}
			break;
		}
		case 'weekly':
		{
			$repeat     = $_row['repeat'];
			$start_time = mktime(0,   0,  0, date('n'), date('d'), date('Y'));
			$end_time   = mktime(23, 59, 59, date('n'), date('t'), date('Y'));
			for($time = $start_time; $time < $end_time; $time += 86400)
			{
				if(strpos($repeat, date('N', $time)) !== false)
				{
					$_row['repeat'] = date('Y-m-d', $time);
					$_plans[] = $_row;
				}
			}
			break;
		}
		case 'monthly':
		{
			if($_row['repeat'] >= date('j'))
			{
				$_row['repeat'] = date('Y-m-').$_row['repeat'];
				$_plans[] = $_row;
			}
			break;
		}
		case 'none':
		default:
		{
			$_row['repeat'] = strtotime($_row['repeat']);
			if
			(
				$_row['repeat']    >= mktime(0,  0,  0,  date('n'), date('d'), date('Y'))
				&& $_row['repeat'] <  mktime(23, 59, 59, date('n'), date('t'), date('Y'))
			)
			{
				$_row['repeat'] = date('Y-m-d', $_row['repeat']);
				$_plans[] = $_row;
			}
			break;
		}
	}
}
foreach($_plans as &$_plan)
{
	$_result_sum_by_cur[$_plan['currency_id']] += ($_optypes[$_plan['operation_type_id']]['is_income'] ? 1 : -1) * $_plan['amount'];
}

$tplengine = new CTemplateEngine('kassa');
$tplengine->assign('title',       get_param('kassa', 'plans_title'));
$tplengine->assign('_plans',      $_plans);
$tplengine->assign('_optypes',    $_optypes);
$tplengine->assign('_opbytype',   $_opbytype);
$tplengine->assign('_currencies', $_currencies);
$tplengine->assign('_sumbyacc',   $_sumbyacc);
$tplengine->assign('_sumbycur',   $_sumbycur);
$tplengine->assign('_result_sum', $_result_sum_by_cur);

$tplengine->template('plans.tpl');

?>