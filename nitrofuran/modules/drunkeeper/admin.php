<?php

/*
	Страница администрирования модуля drunkeeper
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

$error = false;
$sql   = false;

switch($_REQUEST['page'])
{
	/********************
	 *    admin.tpl     *
	 ********************/
	case 1:
	{
		// добавление типа напитка
		if($_POST['adddrinktypename'])
		{
			$sql = "insert into `".DRUNKEEPER_DRINKTYPES_TABLE."` (`name`) values ('".$DB->EscapeString($_POST['adddrinktypename'])."')";
		}
		// удаление типа напитка
		if($_GET['deldrinktype'])
		{
			$sql = "delete from `".DRUNKEEPER_DRINKTYPES_TABLE."` where `id` = '".(int)$_GET['deldrinktype']."'";
		}
		// редактирование типа напитка
		if($_POST['editdrinktype'])
		{
			$sql = "update `".DRUNKEEPER_DRINKTYPES_TABLE."` set `name` = '".$DB->EscapeString($_POST['name'])."'
				where `id` = '".(int)$_POST['editdrinktype']."'";
		}
		// добавление напитка
		if($_POST['adddrinkname'])
		{
			$sql = "insert into `".DRUNKEEPER_DRINKS_TABLE."` (`name`, `type_id`, `strength`) values
				('".$DB->EscapeString($_POST['adddrinkname'])."', '".(int)$_POST['group']."', '".(float)str_replace(',', '.', $_POST['adddrinkstrength'])."')";
		}
		// удаление напитка
		if($_GET['deldrink'])
		{
			$sql = "delete from `".DRUNKEEPER_DRINKS_TABLE."` where `id` = '".(int)$_GET['deldrink']."'";
		}
		// редактирование напитка
		if($_POST['editdrink'])
		{
			$sql = "update `".DRUNKEEPER_DRINKS_TABLE."` set `name` = '".$DB->EscapeString($_POST['name'])."',
				`strength` = '".(float)str_replace(',', '.', $_POST['strength'])."'
				where `id` = '".(int)$_POST['editdrink']."'";
		}
		// выполнение запроса
		if($sql)
		{
			$res = $DB->Query($sql);
			redirect('/admin/?module=drunkeeper&page=1');
		}
		$_drink_types = array();
		$res = $DB->Query("select * from `".DRUNKEEPER_DRINKTYPES_TABLE."`");
		while($_r = $DB->Fetch($res))
		{
			$_drink_types[$_r['id']] = $_r;
		}
		$_drinks = array();
		$res = $DB->Query("select * from `".DRUNKEEPER_DRINKS_TABLE."`");
		while($_r = $DB->Fetch($res))
		{
			$_drinks[$_r['id']] = $_r;
			$_drink_types[$_r['type_id']]['drinks'][$_r['id']] = $_r;
		}
		$tplengine->assign('_drinks', $_drinks);
		$tplengine->assign('_drink_types', $_drink_types);
		$admin_tpl_name = 'admin.tpl';
		break;
	}
}

$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/drunkeeper/templates/'.$admin_tpl_name);

?>