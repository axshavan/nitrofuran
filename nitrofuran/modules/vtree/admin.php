<?php

/*
	Страница администрирования модуля виртуального дерева и статичных страниц.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');

// сохранение
if($_REQUEST['saveid'])
{
	$saveid = (int)$_REQUEST['saveid'];
	if($saveid)
	{
		$sql = "update `".TREE_TABLE."` set
			`name`     = '".$DB->EscapeString($_REQUEST['n'])."',
			`module`   = '".$DB->EscapeString($_REQUEST['m'])."',
			`action`   = '".$DB->EscapeString($_REQUEST['a'])."',
			`template` = '".$DB->EscapeString($_REQUEST['t'])."',
			`access`   = '".($_REQUEST['s'] == 'true' ? 1 : 0)."'
			where `id` = '".$saveid."'";
	}
}
// удаление
elseif($_REQUEST['delid'])
{
	$delid = (int)$_REQUEST['delid'];
	$res   = $DB->Query("select count(`id`) as cnt from `".TREE_TABLE."` where `pid` = '".$delid."'");
	$res   = $DB->Fetch($res);
	if($res['cnt'] > 0)
	{
		$tplengine->assign('error_text', 'Невозможно удалить папку, имеющую подпапки');
	}
	else
	{
		$sql = "delete from `".TREE_TABLE."` where `id` = '".$delid."'";
	}
}
// добавление
elseif($_REQUEST['add'])
{
	$pid = (int)$_REQUEST['add'];
	if($pid)
	{
		$sql = "insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values (
			'".$pid."',
			'".$DB->EscapeString($_REQUEST['n'])."',
			'".$DB->EscapeString($_REQUEST['m'])."',
			'".$DB->EscapeString($_REQUEST['a'])."',
			'".$DB->EscapeString($_REQUEST['t'])."',
			'".($_REQUEST['s'] == 'true' ? 1 : 0)."'
		)";
	}
}

// выполнение
if($sql)
{
	if(!$DB->Query($sql))
	{
		$tplengine->assign('error_text', $DB->Error());
	}
	else
	{
		redirect('/admin/?module=vtree&page=1');
	}
}


$res    = $DB->Query("select * from `".TREE_TABLE."` order by `pid` asc, `id` asc");
$_vtree = array();
while($r = $DB->Fetch($res))
{
	$r['parent_id']   = $r['pid'];
	$_vtree[$r['id']] = $r;
}
$graph = new CGraph();
$graph->CreateFromArray($_vtree);
$_vtree = $graph->GetAsArray(true);
$_vtree = $_vtree['children'];

$tplengine->assign('_vtree', $_vtree);
$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/vtree/templates/admin.tpl');

?>