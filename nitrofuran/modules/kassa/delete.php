<?php

/*

Безвозвратное удаление записи из кассы.

*/

require_once('config.php');
global $DB;

$id = (int)$_REQUEST['id'];
if($id)
{
	$DB->Query("delete from `".KASSA_OPERATION_TABLE."` where `id` = '".$id."'");
}
redirect('..');

?>