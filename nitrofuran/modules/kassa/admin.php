<?php

/*
	Страница администрирования кассы.
*/

require_once('config.php');
global $DB;

$sql   = false;
$error = false;

// добавление валюты
if($_REQUEST['addcurrency'])
{
	$sql = "insert into `".KASSA_CURRENCY_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['addcurrency'])."', `symbol` = '".$DB->EscapeString($_REQUEST['symbol'])."'";
}

// удаление валюты
elseif($_REQUEST['delcurrency'])
{
	$res = $DB->Query("select count(`id`) as c from `".KASSA_OPERATION_TABLE."` where `currency_id` = '".(int)$_REQUEST['delcurrency']."'");
	$res = $DB->Fetch($res);
	if(!$res['c'])
	{
		$sql = "delete from `".KASSA_CURRENCY_TABLE."` where `id` = '".(int)$_REQUEST['delcurrency']."'";
	}
	else
	{
		$error = 'Невозможно удалить валюту, так как присутствуют кассовые операции с такой валютой';
	}
}

// редактирование валюты
elseif($_REQUEST['editcurrency'])
{
	$sql = "update `".KASSA_CURRENCY_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['name'])."', `symbol` = '".$DB->EscapeString($_REQUEST['symbol'])."' where `id` = '".(int)$_REQUEST['editcurrency']."'";
}

// добавление счёта
elseif($_REQUEST['addaccount'])
{
	$sql = "insert into `".KASSA_ACCOUNT_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['addaccount'])."'";
}

// удаление счёта
elseif($_REQUEST['delaccount'])
{
	$res = $DB->Query("select count(`id`) as c from `".KASSA_OPERATION_TABLE."` where `account_id` = '".(int)$_REQUEST['delaccount']."'");
	$res = $DB->Fetch($res);
	if(!$res['c'])
	{
		$sql = "delete from `".KASSA_ACCOUNT_TABLE."` where `id` = '".(int)$_REQUEST['delaccount']."'";
	}
	else
	{
		$error = 'Невозможно удалить счёт, так как он содержит кассовые операции';
	}
}

// изменение счёта
elseif($_REQUEST['editaccount'])
{
	$sql = "update `".KASSA_ACCOUNT_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['name'])."' where `id` = '".(int)$_REQUEST['editaccount']."'";
}

// добавление группы типов
elseif($_REQUEST['addoptypegroup'])
{
	$sql = "insert into `".KASSA_OPERATION_TYPE_GROUP_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['addoptypegroup'])."'";
}

// удаление группы типов
elseif($_REQUEST['deloptypegroup'])
{
	$res = $DB->Query("select count(`id`) as c from `".KASSA_OPERATION_TYPE_TABLE."` where `group_id` = '".(int)$_REQUEST['deloptypegroup']."'");
	$res = $DB->Fetch($res);
	if(!$res['c'])
	{
		$sql = "delete from `".KASSA_OPERATION_TYPE_GROUP_TABLE."` where `id` = '".(int)$_REQUEST['deloptypegroup']."'";
	}
	else
	{
		$error = 'Невозможно удалить группу типов операций, так как она содержит типы';
	}
}

// изменение группы типов
elseif($_REQUEST['editoptypegroup'])
{
	$sql = "update `".KASSA_OPERATION_TYPE_GROUP_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['name'])."' where `id` = '".(int)$_REQUEST['editoptypegroup']."'";
}

// добавление типа
elseif($_REQUEST['addoptype'])
{
	$sql = "insert into `".KASSA_OPERATION_TYPE_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['addoptype'])."', `group_id` = '".(int)$_REQUEST['group']."', `is_income` = '".($_REQUEST['is_income'] == 'true' ? 1 : 0)."'";
}

// удаление типа
elseif($_REQUEST['deloptype'])
{
	$res = $DB->Query("select count(`id`) as c from `".KASSA_OPERATION_TABLE."` where `type_id` = '".(int)$_REQUEST['deloptype']."'");
	$res = $DB->Fetch($res);
	if(!$res['c'])
	{
		$sql = "delete from `".KASSA_OPERATION_TYPE_TABLE."` where `id` = '".(int)$_REQUEST['deloptype']."'";
	}
	else
	{
		$error = 'Невозможно удалить тип операций, так как существуют операции данного типа';
	}
}

// изменение типа
elseif($_REQUEST['editoptype'])
{
	$sql = "update `".KASSA_OPERATION_TYPE_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['name'])."', `is_income` = '".($_REQUEST['is_income']=='true' ? 1 : 0)."' where `id` = '".(int)$_REQUEST['editoptype']."'";
}

if($sql)
{
	$DB->Query($sql);
	redirect('/admin/?module=kassa&page=1');
}

$_kassa_currency = array();
$_kassa_account  = array();
$_kassa_optype   = array();

$res = $DB->Query("select * from `".KASSA_CURRENCY_TABLE."` order by `id` asc");
while($_r = $DB->Fetch($res))
{
	$_kassa_currency[] = $_r;
}
$res = $DB->Query("select * from `".KASSA_ACCOUNT_TABLE."` order by `id` asc");
while($_r = $DB->Fetch($res))
{
	$_kassa_account[] = $_r;
}
$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_GROUP_TABLE."` order by `id` asc");
while($_r = $DB->Fetch($res))
{
	$_kassa_optype[$_r['id']] = $_r;
}
$res = $DB->Query("select * from `".KASSA_OPERATION_TYPE_TABLE."` order by `id` asc");
while($_r = $DB->Fetch($res))
{
	$_kassa_optype[$_r['group_id']]['operation_types'][] = $_r;
}

$tplengine->assign('error',               $error);
$tplengine->assign('_kassa_currency',     $_kassa_currency);
$tplengine->assign('_kassa_account',      $_kassa_account);
$tplengine->assign('_kassa_optype',       $_kassa_optype);
$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/kassa/templates/admin.tpl');

?>