<?php

/*
	Основная страница кассы.
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
	k.`comment`,
	k.`backtime`
	from `".KASSA_OPERATION_TABLE."` k
	join `".KASSA_CURRENCY_TABLE."` c on (c.`id` = k.`currency_id`)
	join `".KASSA_OPERATION_TYPE_TABLE."` o on (o.`id` = k.`type_id`)
	join `".KASSA_ACCOUNT_TABLE."` a on (a.`id` = k.`account_id`)
	where `backtime` >= '".mktime(0, 0, 0, $filter_from_month, $filter_from_day, $filter_from_year)."'
	and `backtime` <= '".mktime(23, 59, 50, $filter_to_month, $filter_to_day, $filter_to_year)."'"
	.($filter_type    ? " and k.`type_id`    = '".$filter_type."'"    : '')
	.($filter_account ? " and k.`account_id` = '".$filter_account."'" : '');
$sql .= " order by `backtime` desc";
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

// ближайшие планы
$_plans = array();
$res = $DB->Query("select * from `".KASSA_PLANS_TABLE."` where `active`");
while($_row = $DB->Fetch($res))
{
	switch($_row['repeat_type'])
	{
		case 'daily':
		{
			for($i = 0; $i < 14; $i++)
			{
				$_plans[date('Y-m-d', time() + 86400 * $i)][] = $_row;
			}
			break;
		}
		case 'weekly':
		{
			for($i = 0; $i < 14; $i++)
			{
				if(strpos($_row['repeat'], date('w', time() + $i * 86400)) !== false)
				{
					$_plans[date('Y-m-d', time() + 86400 * $i)][] = $_row;
				}
			}
			break;
		}
		case 'monthly':
		{
			$_mdays = explode(',', $_row['repeat']);
			foreach($_mdays as $mday)
			{
				$mtime = mktime(0, 0, 0, date('n'), $mday, date('Y'));
				if($mtime > time() - 86400 && $mtime < time() + 14* 86400)
				{
					$_plans[date('Y-m-d', $mtime)][] = $_row;
				}
				$mtime = mktime(0, 0, 0, date('n') + 1, $mday, date('Y'));
				if($mtime > time() - 86400 && $mtime < time() + 14* 86400)
				{
					$_plans[date('Y-m-d', $mtime)][] = $_row;
				}
			}
		}
		case 'none':
		default:
		{
			$_row['repeat'] = date('Y-m-d', strtotime($_row['repeat']));
			if($_row['repeat'] > date('Y-m-d'))
			{
				$_plans[$_row['repeat']][] = $_row;
			}
			break;
		}
	}
}
ksort($_plans);
$tplengine->assign('_plans', $_plans);

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
$filter_from_day   = (int)$filter_from_day;
$filter_from_month = (int)$filter_from_month;
$_filter_from_calendar = array();
$d = mktime(0, 0, 0, $filter_from_month, 2 - date('N', mktime(0, 0, 0, $filter_from_month, 1, $filter_from_year)), $filter_from_year);
$bBreak = false;
while(true)
{
	$_week  = array();
	for($i = 0; $i < 7; $i++)
	{
		$_week[] = array(
			'class' => (date('n', $d) != $filter_from_month ? 'grey' : '').' '.(date('Ymd', $d) == $filter_from ? 'active' : '').' '.(date('N', $d) > 5 ? 'end' : '').($d == strtotime('today') ? ' today' : ''),
			'href'  => string_request_replace('from', date('Ymd', $d)),
			'text'  => date('j', $d)
		);
		$d += 86400;
		switch(date('H', $d))
		{
			// при переводе часов на зимнее время
			case 23:
			{
				$d += 3600;
				break;
			}
			// при переводе часов на летнее время
			case 1:
			{
				$d -= 3600;
				break;
			}
			default:
			{
				break;
			}
		}
		if(date('n', $d) > $filter_from_month || (date('n', $d) < $filter_from_month - 10)) // следующий месяц или следующий год
		{
			$bBreak = true;
		}
		if(date('n', $d) == $filter_from_month)
		{
			$bBreak = false;
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
$bBreak = false;
while(true)
{
	$_week  = array();
	for($i = 0; $i < 7; $i++)
	{
		$_week[] = array(
			'class' => (date('n', $d) != $filter_to_month ? 'grey' : '').' '.(date('Ymd', $d) == $filter_to ? 'active' : '').' '.(date('N', $d) > 5 ? 'end' : '').($d == strtotime('today') ? ' today' : ''),
			'href'  => string_request_replace('to', date('Ymd', $d)),
			'text'  => date('j', $d)
		);
		$d += 86400;
		switch(date('H', $d))
		{
			// при переводе часов на зимнее время
			case 23:
			{
				$d += 3600;
				break;
			}
			// при переводе часов на летнее время
			case 1:
			{
				$d -= 3600;
				break;
			}
			default:
			{
				break;
			}
		}
		if(date('n', $d) > $filter_to_month || (date('n', $d) < $filter_to_month - 10)) // следующий месяц или следующий год
		{
			$bBreak = true;
		}
		if(date('n', $d) == $filter_to_month)
		{
			$bBreak = false;
		}
	}
	$_filter_to_calendar[] = $_week;
	if($bBreak)
	{
		break;
	}
}
$tplengine->assign('_filter_to_calendar', $_filter_to_calendar);

// должники
$res = $DB->Query("select d.`id`, d.`name`, sum(o.`amount`) as 'amount', c.`symbol`
	from `".KASSA_DEBTORS_TABLE."` d
	left join `".KASSA_DEBTORS_OPERATION_TABLE."` o on (o.`debtor_id` = d.`id`)
	left join `".KASSA_CURRENCY_TABLE."` c          on (o.`currency_id` = c.`id`)
	group by d.`id`, o.`currency_id` order by d.`id`");
$_debtors  = array();
while($r = $DB->Fetch($res))
{
	$r['amount'] = (float)$r['amount'];
	$_debtors[] = $r;
}
$tplengine->assign('_debtors', $_debtors);

// типы операций должников
if(isset($_REQUEST['debtor']))
{
	$debtor_id = (int)$_REQUEST['debtor'];
	$res = $DB->Query("select * from `".KASSA_DEBTORS_OPERATION_TABLE."` where `debtor_id` = '".$debtor_id."' order by `id` desc");
	$_debtor_operations = array();
	while($_op = $DB->Fetch($res))
	{
		$_debtor_operations[] = $_op;
	}
	$tplengine->assign('_debtor_operations', $_debtor_operations);
}

// наиболее частые типы операций
$res = $DB->Query("select t.`id` as tid, t.`name` as tname,
	g.`id` as gid, g.`name` as gname from
	`".KASSA_OPERATION_TABLE."` o
	join `".KASSA_OPERATION_TYPE_TABLE."` t on (t.`id` = o.`type_id`)
	join `".KASSA_OPERATION_TYPE_GROUP_TABLE."` g on (g.`id` = t.`group_id`)
	group by t.`id`
	order by count(o.`id`) desc
	limit 5");
$_frequent_types = array();
while($r = $DB->Fetch($res))
{
	$_frequent_types[] = $r;
}
$tplengine->assign('_frequent_types', $_frequent_types);

if(get_param('kassa', 'use_mobile_templates'))
{
	// попробуем угадать, не используется ли мобильное устройство
	if
	(
		strpos($_SERVER['HTTP_USER_AGENT'], 'SymbianOS') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'SymbOS') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== false
		|| strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mobi') !== false
	)
	{
		$tplengine->template('index_240px.tpl');
	}
	else
	{
		$tplengine->template('index.tpl');
	}
}
else
{
	$tplengine->template('index.tpl');
}

?>