<?php

/*
	Основная страница кассы.
*/

require_once('config.php');
global $DB;

// фильр даты ОТ
$filter_from = preg_replace('`\D`', '', $_REQUEST['from']);
if(!$filter_from)
{
	$filter_from = date('Ymd', time() - 86400 * 30);
}
$filter_from_year  = substr($filter_from, 0, 4);
$filter_from_month = substr($filter_from, 4, 2);
$filter_from_day   = substr($filter_from, 6, 2);

// фильтр даты ДО
$filter_to = preg_replace('`\D`', '', $_REQUEST['to']);
if(!$filter_to)
{
	$filter_to = date('Ymd');
}
$filter_to_year  = substr($filter_to, 0, 4);
$filter_to_month = substr($filter_to, 4, 2);
$filter_to_day   = substr($filter_to, 6, 2);

$tplengine = new CTemplateEngine('kassa');
$tplengine->assign('title', get_param('kassa', 'title'));

// фильтр по типу
if(isset($_REQUEST['type']))
{
	$filter_type = (int)$_REQUEST['type'];
	if($filter_type)
	{
		$tplengine->assign('filter_type', $filter_type);
	}
}

// фильтр по счёту
if(isset($_REQUEST['account']))
{
	$filter_account = (int)$_REQUEST['account'];
	if($filter_account)
	{
		$tplengine->assign('filter_account', $filter_account);
	}
}

// кассовые операции
$_operations   = array();
$_sum_filtered = array();
$sql = "select
	k.`id`,
	c.`symbol`     as currency_symbol,
	c.`id`         as currency_id,
	c.`name`       as currency,
	a.`id`         as account_id,
	a.`name`       as account,
	o.`id`         as optype_id,
	o.`name`       as optype,
	o.`is_income`  as income,
	o.`is_service` as service,
	k.`amount`,
	k.`time`,
	k.`comment`
	from `".KASSA_OPERATION_TABLE."` k
	join `".KASSA_CURRENCY_TABLE."` c on (c.`id` = k.`currency_id`)
	join `".KASSA_OPERATION_TYPE_TABLE."` o on (o.`id` = k.`type_id`)
	join `".KASSA_ACCOUNT_TABLE."` a on (a.`id` = k.`account_id`)
	where `time` > '".mktime(0, 0, 0, $filter_from_month, $filter_from_day, $filter_from_year)."'
	and `time` < '".mktime(23, 59, 50, $filter_to_month, $filter_to_day, $filter_to_year)."'"
	.($filter_type    ? " and k.`type_id`    = '".$filter_type."'"    : '')
	.($filter_account ? " and k.`account_id` = '".$filter_account."'" : '');
$sql .= " order by `id` desc";
$res = $DB->Query($sql);
while($_row = $DB->Fetch($res))
{
	$_operations[] = $_row;
	$_sum_filtered[$_row['currency_symbol']] += $_row['service'] ? 0 : (($_row['income'] ? 1 : -1) * $_row['amount']);
}
$tplengine->assign('_operations',   $_operations);
$tplengine->assign('_sum_filtered', $_sum_filtered);

// в кассе всего
$_sum_all = array();
$res = $DB->Query("select sum(k.`amount` * if(o.`is_income`, 1, -1)) as s, c.`symbol`, a.`name`
	from `".KASSA_OPERATION_TABLE."` k
	join `".KASSA_ACCOUNT_TABLE."` a on (a.`id` = k.`account_id`)
	join `".KASSA_OPERATION_TYPE_TABLE."` o on (o.`id` = k.`type_id`)
	join `".KASSA_CURRENCY_TABLE."` c on (c.`id` = k.`currency_id`)
	group by a.`name`, c.`symbol`");
while($_row = $DB->Fetch($res))
{
	$_sum_all[$_row['name']][$_row['symbol']] = $_row['s'];
}
$tplengine->assign('_sum_all', $_sum_all);

// группы типов операций
$_optypegroups = array();
$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_GROUP_TABLE."`");
while($_row = $DB->Fetch($res))
{
	$_optypegroups[$_row['id']] = $_row;
}
$tplengine->assign('_optypegroups', $_optypegroups);

// типы операций
$_optypes = array();
$group    = 0;
$res      = $DB->Query("select * from `".KASSA_OPERATION_TYPE_TABLE."` order by `group_id` asc");
while($_row = $DB->Fetch($res))
{
	if($group != $_row['group_id'])
	{
		$group = $_row['group_id'];
		$_optypes[$group] = array();
	}
	$_optypes[$group][] = $_row;
	if($_row['id'] == $filter_type)
	{
		$tplengine->assign('show_group', $group);
	}
}
$tplengine->assign('_optypes', $_optypes);

// валюты
$_currencies = array();
$res = $DB->Query("select * from `".KASSA_CURRENCY_TABLE."`");
while($_row = $DB->Fetch($res))
{
	$_currencies[] = $_row;
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

$tplengine->assign('filter_from_year',  $filter_from_year);
$tplengine->assign('filter_from_month', $filter_from_month);
$tplengine->assign('filter_from_day',   $filter_from_day);

$tplengine->assign('href_filter_from_prevyear',  string_request_replace('from', ($filter_from_year - 1).$filter_from_month.$filter_from_day));
$tplengine->assign('href_filter_from_nextyear',  string_request_replace('from', ($filter_from_year + 1).$filter_from_month.$filter_from_day));
$tplengine->assign('href_filter_from_prevmonth', string_request_replace('from', date('Ymd', mktime(0, 0, 0, $filter_from_month - 1, $filter_from_day, $filter_from_year))));
$tplengine->assign('href_filter_from_nextmonth', string_request_replace('from', date('Ymd', mktime(0, 0, 0, $filter_from_month + 1, $filter_from_day, $filter_from_year))));

$tplengine->assign('filter_to_year',  $filter_to_year);
$tplengine->assign('filter_to_month', $filter_to_month);
$tplengine->assign('filter_to_day',   $filter_to_day);

$tplengine->assign('href_filter_to_prevyear',  string_request_replace('to', ($filter_to_year - 1).$filter_to_month.$filter_to_day));
$tplengine->assign('href_filter_to_nextyear',  string_request_replace('to', ($filter_to_year + 1).$filter_to_month.$filter_to_day));
$tplengine->assign('href_filter_to_prevmonth', string_request_replace('to', date('Ymd', mktime(0, 0, 0, $filter_to_month - 1, $filter_to_day, $filter_to_year))));
$tplengine->assign('href_filter_to_nextmonth', string_request_replace('to', date('Ymd', mktime(0, 0, 0, $filter_to_month + 1, $filter_to_day, $filter_to_year))));

// формирование календаря фильтра даты ОТ
$_filter_from_calendar = array();
$d = mktime(0, 0, 0, $filter_from_month, 2 - date('N', mktime(0, 0, 0, $filter_from_month, 1, $filter_from_year)), $filter_from_year);
while(true)
{
	$_week  = array();
	$bBreak = false;
	for($i = 0; $i < 7; $i++)
	{
		$_week[] = array(
			'class' => (date('n', $d) != $filter_from_month ? 'grey' : '').' '.(date('Ymd', $d) == $filter_from ? 'active' : '').' '.(date('N', $d) > 5 ? 'end' : '').($d == strtotime('today') ? ' today' : ''),
			'href'  => string_request_replace('from', date('Ymd', $d)),
			'text'  => date('j', $d)
		);
		$d += 86400;
		if(date('n', $d) > $filter_from_month)
		{
			$bBreak = true;
		}
	}
	$_filter_from_calendar[] = $_week;
	if($bBreak)
	{
		break;
	}
}
$tplengine->assign('_filter_from_calendar', $_filter_from_calendar);

// формирование календаря фильтра даты ДО
$_filter_to_calendar = array();
$d = mktime(0, 0, 0, $filter_to_month, 2 - date('N', mktime(0, 0, 0, $filter_to_month, 1, $filter_to_year)), $filter_to_year);
while(true)
{
	$_week  = array();
	$bBreak = false;
	for($i = 0; $i < 7; $i++)
	{
		$_week[] = array(
			'class' => (date('n', $d) != $filter_to_month ? 'grey' : '').' '.(date('Ymd', $d) == $filter_to ? 'active' : '').' '.(date('N', $d) > 5 ? 'end' : '').($d == strtotime('today') ? ' today' : ''),
			'href'  => string_request_replace('to', date('Ymd', $d)),
			'text'  => date('j', $d)
		);
		$d += 86400;
		if(date('n', $d) > $filter_to_month)
		{
			$bBreak = true;
		}
	}
	$_filter_to_calendar[] = $_week;
	if($bBreak)
	{
		break;
	}
}
$tplengine->assign('_filter_to_calendar', $_filter_to_calendar);

$tplengine->template('index.tpl');

?>