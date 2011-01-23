<?php

/*
	Страница администрирования кассы.
*/

require_once('config.php');
global $DB;

$sql   = false;
$error = false;

switch($_REQUEST['page'])
{
	/********************
	 *    admin.tpl     *
	 ********************/
	case 1:
	{
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
			$sql = "insert into `".KASSA_OPERATION_TYPE_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['addoptype'])."', `group_id` = '".(int)$_REQUEST['group']."', `is_income` = '".($_REQUEST['kassa_addoptype'.((int)$_REQUEST['group']).'_form_income'] ? 1 : 0)."', `is_service` = '".($_REQUEST['is_service'] == 'true' ? 1 : 0)."'";
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
			$sql = "update `".KASSA_OPERATION_TYPE_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['name'])."', `is_income` = '".(isset($_REQUEST['is_income']) ? 1 : 0)."', `is_service` = '".(isset($_REQUEST['is_service']) ? 1 : 0)."' where `id` = '".(int)$_REQUEST['editoptype']."'";
		}
		
		if($sql)
		{
			$DB->Query($sql);
			redirect('/admin/?module=kassa&page=1');
			die();
		}
		
		$admin_tpl_name = 'admin.tpl';
		break;
	}
	/*************************
	 *    admin_plan.tpl     *
	 *************************/
	case 2:
	{
		// редактирование плана
		if($_REQUEST['editplan'])
		{
			$_REQUEST['repeat'] = str_replace(',,', ',', trim($_REQUEST['repeat'], ','));
			$sql = "update `".KASSA_PLANS_TABLE."` set
				`name`              = '".$DB->EscapeString($_REQUEST['name'])."',
				`operation_type_id` = '".(int)$_REQUEST['optype']."',
				`amount`            = '".(float)$_REQUEST['amount']."',
				`repeat_type`       = '".$DB->EscapeString($_REQUEST['type'])."',
				`repeat`            = '".$DB->EscapeString($_REQUEST['repeat'])."',
				`active`            = '".($_REQUEST['active'] == 'true')."'
				where `id` = '".(int)$_REQUEST['editplan']."'";
		}
		
		// удаление плана
		if($_REQUEST['delplan'])
		{
			$sql = "delete from `".KASSA_PLANS_TABLE."` where `id` = '".(int)$_REQUEST['delplan']."'";
		}
		
		// добавление плана
		if(isset($_REQUEST['addplan']))
		{
			$_REQUEST['repeat'] = str_replace(',,', ',', trim($_REQUEST['repeat'], ','));
			$sql = "insert into `".KASSA_PLANS_TABLE."`
				(`name`, `operation_type_id`, `amount`, `repeat_type`, `repeat`, `active`)
				values (
					'".$DB->EscapeString($_REQUEST['name'])."',
					'".(int)$_REQUEST['optype']."',
					'".(float)$_REQUEST['amount']."',
					'".$DB->EscapeString($_REQUEST['repeat_type'])."',
					'".$DB->EscapeString($_REQUEST['repeat'])."',
					'".($_REQUEST['active'] == 'true')."'
				)";
		}
		
		if($sql)
		{
			$DB->Query($sql);
			redirect('/admin/?module=kassa&page=2');
			die();
		}
		
		// список планируемых операций
		$_plans = array();
		$res = $DB->Query("select * from `".KASSA_PLANS_TABLE."`");
		while($_r = $DB->Fetch($res))
		{
			$_plans[$_r['id']] = $_r;
		}
		$tplengine->assign('_plans', $_plans);
		
		$admin_tpl_name = 'admin_plan.tpl';
		break;
	}
	/**************************
	 *    admin_debtor.tpl    *
	 **************************/
	case 3:
	{
		// редактирование должника
		if($_REQUEST['edit'])
		{
			$id = (int)$_REQUEST['edit'];
			if($id)
			{
				$sql = "update `".KASSA_DEBTORS_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['name'])."' where `id` = '".$id."'";
			}
		}
		
		// добавление должника
		if($_REQUEST['add'])
		{
			$sql = "insert into `".KASSA_DEBTORS_TABLE."` set `name` = '".$DB->EscapeString($_REQUEST['add'])."'";
		}
		
		// удаление должника
		if($_REQUEST['delete'])
		{
			$id = (int)$_REQUEST['delete'];
			if($id)
			{
				$DB->Query("delete from `".KASSA_DEBTORS_OPERATION_TABLE."` where `debtor_id` = '".$id."'");
				$sql = "delete from `".KASSA_DEBTORS_TABLE."` where `id` = '".$id."'";
			}
		}
		
		// привнесение измнений
		if($sql)
		{
			$DB->Query($sql);
			redirect('/admin/?module=kassa&page=3');
			die();
		}
		
		// список должников и кредиторов
		$res = $DB->Query("select `id`, `name` from `".KASSA_DEBTORS_TABLE."`");
		$_debtors = array();
		while($r = $DB->Fetch($res))
		{
			$_debtors[$r['id']] = $r;
		}
		$tplengine->assign('_debtors', $_debtors);
		$admin_tpl_name = 'admin_debtor.tpl';
		break;
	}
	/*************************
	 *   admin_optypes.tpl   *
	 *************************/
	case 4:
	{
		// свойства операций
		$_kassa_properties = $DB->QueryFetched("select * from `".KASSA_OPERATION_PROPNAMES_TABLE."`");
		$tplengine->assign('_kassa_properties', $_kassa_properties);
		
		// значения свойств операций
		$res = $DB->Query("select * from `".KASSA_OPERATION_PROPVALUES_TABLE."`");
		while($_row = $DB->Fetch($res))
		{
			$_kassa_propvalues[$_row['operation_type_id']][$_row['option_id']] = $_row['value'];
		}
		$tplengine->assign('_kassa_propvalues', $_kassa_propvalues);
		
		// редактирование типа операций
		if($_REQUEST['optype_id'])
		{
			$optype_id = (int)$_REQUEST['optype_id'];
			if($optype_id)
			{
				// основные свойства
				$sql = "update `".KASSA_OPERATION_TYPE_TABLE."` set
					`name`       = '".$DB->EscapeString($_REQUEST['name'])."',
					`is_income`  = '".($_REQUEST['is_income']  ? 1 : 0)."',
					`is_service` = '".($_REQUEST['is_service'] ? 1 : 0)."'
					where `id` = '".$optype_id."'";
				$DB->Query($sql);
				
				// дополнительные свойства
				foreach($_kassa_properties as $_prop)
				{
					$sql = false;
					if($_prop['type'] == 'checkbox')
					{
						$sql = "replace into `".KASSA_OPERATION_PROPVALUES_TABLE."` set `value` = '".($_REQUEST['prop_'.$_prop['id']] ? 1 : 0)."', `operation_type_id` = '".$optype_id."', `option_id` = '".$_prop['id']."'";
					}
					else
					{
						$sql = "replace into `".KASSA_OPERATION_PROPVALUES_TABLE."` set `value` = '".$DB->EscapeString($_REQUEST['prop_'.$_prop['id']])."', `operation_type_id` = '".$optype_id."', `option_id` = '".$_prop['id']."'";
					}
					
					// выполнение запроса
					if($sql)
					{
						$DB->Query($sql);
					}
				}
			}
			redirect('/admin/?module=kassa&page=4');
			die();
		}
		
		// добавление нового свойства
		if($_REQUEST['newpropname'] && $_REQUEST['newproptype'])
		{
			$DB->Query("insert into `".KASSA_OPERATION_PROPNAMES_TABLE."` (`name`, `type`)
				values ('".$DB->EscapeString($_REQUEST['newpropname'])."', '".($_REQUEST['newproptype'] == 'text' ? 'text' : 'checkbox')."')");
			redirect('/admin/?module=kassa&page=4');
			die();
		}
		
		// удаление свойства
		$delproperty = (int)$_REQUEST['delproperty'];
		if($delproperty)
		{
			$DB->TransactionStart();
			$DB->Query("delete from `".KASSA_OPERATION_PROPNAMES_TABLE."` where `id` = '".$delproperty."'");
			$DB->Query("delete from `".KASSA_OPERATION_PROPVALUES_TABLE."` where `option_id` = '".$delproperty."'");
			$DB->TransactionCommit();
			redirect('/admin/?module=kassa&page=4');
			die();
		}
		
		$admin_tpl_name = 'admin_optypes.tpl';
		break;
	}
}

$_kassa_currency    = array();
$_kassa_account     = array();
$_kassa_optype      = array();
$_kassa_optype_byid = array();

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
	$_kassa_optype_byid[$_r['id']] = $_r;
}

$tplengine->assign('error',               $error);
$tplengine->assign('_kassa_currency',     $_kassa_currency);
$tplengine->assign('_kassa_account',      $_kassa_account);
$tplengine->assign('_kassa_optype',       $_kassa_optype);
$tplengine->assign('_kassa_optype_byid',  $_kassa_optype_byid);
$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/kassa/templates/'.$admin_tpl_name);

?>