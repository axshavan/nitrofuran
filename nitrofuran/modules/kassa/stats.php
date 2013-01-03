<?php

/*
	Статистика по кассе.
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

$tplengine = new CTemplateEngine('kassa');
$tplengine->assign('title', get_param('kassa', 'stats_title'));

// валюты
$_currencies = array();
$res = $DB->Query("select * from `".KASSA_CURRENCY_TABLE."`");
while($_row = $DB->Fetch($res))
{
	$_currencies[$_row['id']] = $_row;
}
$tplengine->assign('_currencies', $_currencies);

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
$group          = false;
$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_TABLE."`");
while($_row = $DB->Fetch($res))
{
	if($group !== $_row['group_id'])
	{
		$group = $_row['group_id'];
	}
	$_optypes_g[$group][]  = $_row;
	$_optypes[$_row['id']] = $_row;
}
$tplengine->assign('_optypes_g', $_optypes_g);
$tplengine->assign('_optypes',   $_optypes);

// всякая статистика
$_operation_max   = array();
$_operation_count = array();
$_operation_sum   = array();
$_months          = array();
$_weekdays        = array();
// всякая статистика за последний месяц
$_operation_count_m = array();
$_operation_max_m   = array();
$_operation_sum_m   = array();
$_comments          = array();
$last_month         = time() - 86400 * 31;
$first_operation    = time();

$res = $DB->Query("select * from `kassa_operation`");
while($_row = $DB->Fetch($res))
{
	if($_row['amount'] > $_operation_max[$_row['type_id']][$_row['currency_id']]['amount'])
	{
		$_operation_max[$_row['type_id']][$_row['currency_id']] = $_row;
	}
	$_operation_count[$_row['type_id']][$_row['currency_id']]++;
	$_operation_sum[$_row['type_id']][$_row['currency_id']]  += $_row['amount'];
	if(!$_optypes[$_row['type_id']]['is_service'])
	{
		$_months[date('Y-m', $_row['backtime'])][$_optypes[$_row['type_id']]['is_income'] ? 'income' : 'expenditure'][$_currencies[$_row['currency_id']]['symbol']] += $_row['amount'];
	}
	if($_row['time'] > $last_month)
	{
		if($_row['amount'] > $_operation_max_m[$_row['type_id']][$_row['currency_id']]['amount'])
		{
			$_operation_max_m[$_row['type_id']][$_row['currency_id']] = $_row;
		}
		$_operation_count_m[$_row['type_id']][$_row['currency_id']]++;
		$_operation_sum_m[$_row['type_id']][$_row['currency_id']]  += $_row['amount'];
	}
	if($first_operation > $_row['backtime'])
	{
		$first_operation = $_row['backtime'];
	}
	$_comments[$_row['currency_id']][$_row['comment']]['quantity']++;
	$_comments[$_row['currency_id']][$_row['comment']]['sum'] += $_row['amount'];
	$_weekdays[date('N', $_row['backtime'])]['opnum']++;
	$_weekdays[date('N', $_row['backtime'])]['opnum_c'][$_row['currency_id']]++;
}
krsort($_months);
foreach($_months as $k => &$_m)
{
	$_m['name'] = explode('-', $k);
	$_m['name'] = rudate('M Y', mktime(0, 0, 0, $_m['name'][1] + 1, 0, $_m['name'][0]));
}

// комментарии
foreach($_comments as &$_cur)
{
	unset($_cur['']);
	foreach($_cur as &$c)
	{
		$c['average'] = round($c['sum'] / $c['quantity'], 2);
	}
}

$_comments_max_quantity = array();
$_comments_max_sum      = array();
$_comments_max_average  = array();
foreach($_comments as $cur_id => &$_cur)
{
	uasort($_cur, create_function('$a, $b', 'return $a["quantity"] == $b["quantity"] ? 0 : ($a["quantity"] < $b["quantity"] ? 1 : -1);'));
	$_comments_max_quantity[$cur_id] = array_slice($_cur, 0, 10, true);
	uasort($_cur, create_function('$a, $b', 'return $a["sum"] == $b["sum"] ? 0 : ($a["sum"] < $b["sum"] ? 1 : -1);'));
	$_comments_max_sum[$cur_id] = array_slice($_cur, 0, 10, true);
	uasort($_cur, create_function('$a, $b', 'return $a["average"] == $b["average"] ? 0 : ($a["average"] < $b["average"] ? 1 : -1);'));
	$_comments_max_average[$cur_id] = array_slice($_cur, 0, 10, true);
}
unset($_comments);
$tplengine->assign('_comments_max_quantity', $_comments_max_quantity);
$tplengine->assign('_comments_max_sum',      $_comments_max_sum);
$tplengine->assign('_comments_max_average',  $_comments_max_average);

// количество месяцев, за которое мы собираем статистику (ну, сколько всего касса работает)
$first_operation = explode(' ', date('Y n', $first_operation));
$kassa_action_time = explode(' ', date('Y n'));
$kassa_action_time = ($kassa_action_time[0] - $first_operation[0]) * 12 + $kassa_action_time[1] - $first_operation[1];

$tplengine->assign('_operation_max',     $_operation_max);
$tplengine->assign('_operation_count',   $_operation_count);
$tplengine->assign('_operation_sum',     $_operation_sum);
$tplengine->assign('_operation_max_m',   $_operation_max_m);
$tplengine->assign('_operation_count_m', $_operation_count_m);
$tplengine->assign('_operation_sum_m',   $_operation_sum_m);
$tplengine->assign('_months',            $_months);
$tplengine->assign('kassa_action_time',  $kassa_action_time);
$tplengine->assign('_weekdays',          $_weekdays);

$tplengine->template('stats.tpl');

?>