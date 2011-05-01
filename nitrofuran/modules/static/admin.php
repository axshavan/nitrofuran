<?php

/*
	Административная часть модуля статических страниц.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require_once('config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');

switch($page)
{
	case 1:
	{
		// выборка папок виртуального дерева
		$res = $DB->Query("select t.*, s.`id` as static_page_id
			from `".TREE_TABLE."` t
			left join `".STATIC_PAGES_TABLE."` s on s.`tree_id` = t.id
			order by t.`pid` asc, t.`id` asc");
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
		$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/static/templates/admin_tree.tpl');
		break;
	}
	case 2:
	{
		// выборка статичных страниц
		$res    = $DB->Query("select * from `".STATIC_PAGES_TABLE."` order by `id` desc");
		$_pages = array();
		while($_row = $DB->Fetch($res))
		{
			$_pages[$_row['id']] = $_row;
		}
		$tplengine->assign('_pages', $_pages);
		$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/static/templates/admin_table.tpl');
		break;
	}
	case 3:
	{
		break;
	}
}


?>