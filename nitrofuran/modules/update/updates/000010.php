<?php

// 000010
// добавление подпапки ajax в папку kassa

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
$res  = $DB->Query("select `id`, `access` from `".TREE_TABLE."` where `name` = 'kassa' and `module` = 'kassa' and `action` = ''");
$_row = $DB->Fetch($res);
if(!$_row['id'])
{
	return false;
}
return $DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$_row['id']."', 'ajax', 'kassa', 'ajax', '', '".$_row['access']."')");

?>