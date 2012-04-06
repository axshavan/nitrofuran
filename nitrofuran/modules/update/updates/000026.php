<?php

// 000026
// добавление в кассу формы обмена валют

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->TransactionStart();

// добавление странички
$res  = $DB->Query("select `id` from `".TREE_TABLE."` where `action` = '' and `module` = 'kassa'");
$_row = $DB->Fetch($res);
if(!$_row['id'])
{
	$DB->TransactionRollback();
	return false;
}
$res = $DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$_row['id']."', 'tran_cur', 'kassa', 'tran_cur', '', 0)");
if($res === false)
{
	$DB->TransactionRollback();
	return false;
}

// тип операций "обмен валюты +"
$res  = $DB->Query("select `id` from `".KASSA_OPERATION_TYPE_GROUP_TABLE."` where `name` = 'Прибыль'");
$_row = $DB->Fetch($res);
$group_id = $_row['id'];
if(!$group_id)
{
	$group_id = 1;
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$group_id."', 'Обмен валюты +', 1, 0)");
$insert_id = $DB->InsertedId();
if(!$insert_id)
{
	return false;
}
new_param('kassa', 'OPTYPE_CUREXCH_PLUS', 'Обмен валюты +', 'text', $insert_id);

// тип операций "обмен валюты -"
$res  = $DB->Query("select `id` from `".KASSA_OPERATION_TYPE_GROUP_TABLE."` where `name` = 'Прочее'");
$_row = $DB->Fetch($res);
$group_id = $_row['id'];
if(!$group_id)
{
	$group_id = 1;
}
$DB->Query("insert into `".KASSA_OPERATION_TYPE_TABLE."` (`group_id`, `name`, `is_income`, `is_service`) values
	('".$group_id."', 'Обмен валюты -', 0, 0)");
$insert_id = $DB->InsertedId();
if(!$insert_id)
{
	return false;
}
new_param('kassa', 'OPTYPE_CUREXCH_MINUS', 'Обмен валюты -', 'text', $insert_id);

$DB->TransactionCommit();

?>