<?php

/*
	Статистика по кассе.
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
// всякая статистика за последний месяц
$_operation_count_m = array();
$_operation_max_m   = array();
$_operation_sum_m   = array();
$last_month         = time() - 86400 * 31;

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
		$_months[date('Y-m', $_row['time'])][$_optypes[$_row['type_id']]['is_income'] ? 'income' : 'expenditure'] += $_row['amount'];
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
}
krsort($_months);
foreach($_months as $k => &$_m)
{
	$_m['name'] = explode('-', $k);
	$_m['name'] = rudate('M Y', mktime(0, 0, 0, $_m['name'][1] + 1, 0, $_m['name'][0]));
}

$tplengine->assign('_operation_max',     $_operation_max);
$tplengine->assign('_operation_count',   $_operation_count);
$tplengine->assign('_operation_sum',     $_operation_sum);
$tplengine->assign('_operation_max_m',   $_operation_max_m);
$tplengine->assign('_operation_count_m', $_operation_count_m);
$tplengine->assign('_operation_sum_m',   $_operation_sum_m);
$tplengine->assign('_months',            $_months);

$tplengine->template('stats.tpl');

?>