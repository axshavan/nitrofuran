<?php

/*
 * Редактирование параметров доступа или их категорий.
 */

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kokol/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kokol/lib.php');
global $DB;

$DB->TransactionStart();

// добавление категории
if(strlen($_REQUEST['addcat']))
{
	$sql = "insert into `".KOKOL_CATEGORIES_TABLE."` (`pid`, `name`) values
		('".(int)$_REQUEST['root_cat']."', '".kokol_encode($_REQUEST['addcat'])."')";
}

// добавление параметра доступа
elseif(strlen($_REQUEST['addpwdl']) && strlen($_REQUEST['addpwdp']))
{
	$sql = "insert into `".KOKOL_PASSWORDS_TABLE."` (`category_id`, `resource`, `login`, `password`) values
		('".(int)$_REQUEST['root_cat']."', '".kokol_encode($_REQUEST['addpwdr'])."', '".kokol_encode($_REQUEST['addpwdl'])."', '".kokol_encode($_REQUEST['addpwdp'])."')";
}

// изменение параметров доступа
elseif((int)($_REQUEST['updpwdid']))
{
	$sql = "update `".KOKOL_PASSWORDS_TABLE."` set
		`resource` = '".kokol_encode($_REQUEST['updpwdr'])."',
		`login`    = '".kokol_encode($_REQUEST['updpwdl'])."',
		`password` = '".kokol_encode($_REQUEST['updpwdp'])."'
		where `id` = '".(int)$_REQUEST['updpwdid']."'";
}

// удаление параметра доступа
elseif((int)($_REQUEST['delpwd']))
{
	$sql = "delete from `".KOKOL_PASSWORDS_TABLE."` where `id` = '".(int)$_REQUEST['delpwd']."'";
}

// выполнение скрипта
if(strlen($sql))
{
	$res = $DB->Query($sql);
	if(!$res)
	{
		$error_text = $DB->Error();
		$DB->TransactionRollback();
	}
}
$DB->TransactionCommit();
redirect(HTTP_ROOT.'/kokol/');

?>