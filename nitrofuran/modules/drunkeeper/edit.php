<?php

/*
	Drunkeeper edit file - редактирование, добавление и удаление записей
	из публичной части.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require('config.php');
global $DB;
$tplengine     = new CTemplateEngine('drunkeeper');
$template_name = 'edit_list.tpl';

// список типов напитков
$_drink_types = array();
$res          = $DB->Query("select * from `".DRUNKEEPER_DRINKTYPES_TABLE."` order by `name` asc");
while($_r = $DB->Fetch($res))
{
	$_drink_types[$_r['id']] = $_r;
}

// список напитков
$_drinks = array();
$res     = $DB->Query("select * from `".DRUNKEEPER_DRINKS_TABLE."` order by `name` asc");
while($_r = $DB->Fetch($res))
{
	$_drinks[$_r['id']] = $_r;
	$_drink_types[$_r['type_id']]['drinks'][$_r['id']] = $_r;
}
$tplengine->assign('_drinks',      $_drinks);
$tplengine->assign('_drink_types', $_drink_types);

if($_POST)
{
	if($_POST['delete'])
	{
		// удалить запись о выпитом
		$DB->Query("delete from `".DRUNKEEPER_ACTS_TABLE."` where `id` = '".(int)$_POST['delete']."'");
	}
	elseif($_POST['id'])
	{
		// отредактировать запись о выпитом
		$date_drinked = mktime(0, 0, 0, (int)$_POST['month'], (int)$_POST['day'], (int)$_POST['year']);
		$DB->Query("update `".DRUNKEEPER_ACTS_TABLE."` set
			`drink_id`     = '".(int)$_POST['drink_'.$_POST['drinktype']]."',
			`volume`       = '".(int)$_POST['volume']."',
			`comment`      = '".$DB->EscapeString($_POST['comment'])."',
			`date_drinked` = '".$date_drinked."'
			where `id`     = '".(int)$_POST['id']."'");
	}
	else
	{
		// добавить запись
		$date_drinked = mktime(0, 0, 0, (int)$_POST['month'], (int)$_POST['day'], (int)$_POST['year']);
		$DB->Query("insert into `".DRUNKEEPER_ACTS_TABLE."`
			(`drink_id`, `volume`, `date_drinked`, `date_inserted`, `comment`) values
			(
				'".(int)$_POST['drink_'.$_POST['drinktype']]."',
				'".(int)$_POST['volume']."',
				'".$date_drinked."',
				unix_timestamp(),
				'".$DB->EscapeString($_POST['comment'])."'
			)");
	}
	redirect('/drunkeeper/edit/');
}
if(isset($_GET['id']) && $_GET['id'])
{
	// показать форму редактирования записи о выпитом
	// или создания новой записи
	$id = (int)$_GET['id'];
	if($id)
	{
		$_act = $DB->Query("select * from `".DRUNKEEPER_ACTS_TABLE."` where `id` = '".$id."'");
		$_act = $DB->Fetch($_act);
		$tplengine->assign('_act', $_act);
	}
	$tplengine->assign('id', $id);
	$template_name = 'edit_form.tpl';
}
else
{
	// список выпитого
	$_acts = array();
	$res   = $DB->Query("select * from `".DRUNKEEPER_ACTS_TABLE."` order by `date_drinked` desc, `id` desc limit 50");
	while($_r = $DB->Fetch($res))
	{
		$_acts[$_r['id']] = $_r;
	}
	$tplengine->assign('_acts', $_acts);
}

$tplengine->template($template_name);

?>