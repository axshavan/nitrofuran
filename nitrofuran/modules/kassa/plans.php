<?php

/*
	Страница кассы с планированием бюджета на месяц.
*/

require_once('config.php');
global $DB;

if($_REQUEST['switch'])
{
	$switch_id = (int)$_REQUEST['switch'];
	if($switch_id)
	{
		$DB->Query("update `".KASSA_OPERATION_PROPVALUES_TABLE."` set `value` = if(`value` = 1, 0, 1) where `operation_type_id` = '".$switch_id."' and `option_id` = 1");
		if(!$DB->AffectedRows())
		{
			$DB->Query("insert into `".KASSA_OPERATION_PROPVALUES_TABLE."` (`operation_type_id`, `option_id`, `value`) values ('".$switch_id."', '1', '1')");
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
$mprogress = 1 - date('j') / date('t');

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
$res = $DB->Query("select * from `".KASSA_OPERATION_PROPVALUES_TABLE."` where `option_id` = 1");
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
	}
}

$tplengine = new template_engine('kassa');
$tplengine->assign('title', get_param('kassa', 'plans_title'));

$tplengine->assign('_optypes',    $_optypes);
$tplengine->assign('_opbytype',   $_opbytype);
$tplengine->assign('_currencies', $_currencies);
$tplengine->assign('_sumbyacc',   $_sumbyacc);
$tplengine->assign('_sumbycur',   $_sumbycur);
$tplengine->assign('_result_sum', $_result_sum_by_cur);

$tplengine->template('plans.tpl');

?>