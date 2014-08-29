<?php

/**
 * Статистика по кассе за один месяц.
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

require_once('config.php');
global $DB;

$tplengine = new CTemplateEngine('kassa');
$tplengine->assign('title', get_param('kassa', 'stats_title'));
$tplengine->assign('use_blue_template', get_param('kassa', 'use_blue_template'));

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
	$_accounts[$_row['id']] = $_row;
}
$tplengine->assign('_accounts', $_accounts);

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
$group          = false;
$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_TABLE."`");
while($_row = $DB->Fetch($res))
{
	$_optypes[$_row['id']] = $_row;
}
$tplengine->assign('_optypes', $_optypes);

// всякая статистика
$_sum                 = array();
$_sum_by_accounts     = array();
$_sum_by_optypes      = array();
$_sum_by_optypegroups = array();
$_used_currencies     = array();

// выборка и разбор
$date_start = explode('-', $_GET['m']);
$date_end = $date_start;
if(sizeof($date_start) == 2)
{
	if($date_start[1] == 12)
	{
		$date_end[0]++;
		$date_end[1] = 1;
	}
	else
	{
		$date_end[1]++;
	}
	$date_start = strtotime((int)$date_start[0].'-'.(int)$date_start[1].'-01 00:00:00');
	if(!$date_start)
	{
		$date_start = strtotime(date('Y-m').'-01 00:00:00');
		$date_end = $date_start + 31 * 86400;
	}
	else
	{
		$date_end = strtotime((int)$date_end[0].'-'.(int)$date_end[1].'-01 00:00:00');
	}
}
$res = $DB->Query("select * from `".KASSA_OPERATION_TABLE."` where `time` >= '".$date_start."' and `time` <= '".$date_end."'");
while($_row = $DB->Fetch($res))
{
	$amount = $_row['amount'] * ($_optypes[$_row['type_id']]['is_income'] ? 1 : -1);
	$currency = $_currencies[$_row['currency_id']]['symbol'];
	$_used_currencies[$currency] = $currency;
	$_sum[$currency] += $amount;
	$_sum_by_accounts[$_accounts[$_row['account_id']]['name']][$currency] += $amount;
	$optype = $_optypes[$_row['type_id']];
	$_sum_by_optypes[$optype['group_id']][$optype['name']][$currency] += $amount;
	$_sum_by_optypegroups[$optype['group_id']][$currency] += $amount;
}
$tplengine->assign('_sum', $_sum);
$tplengine->assign('_sum_by_accounts', $_sum_by_accounts);
$tplengine->assign('_sum_by_optypes', $_sum_by_optypes);
$tplengine->assign('_sum_by_optypegroups', $_sum_by_optypegroups);
$tplengine->assign('_used_currencies', $_used_currencies);

$tplengine->template('stats_month.tpl');

?>