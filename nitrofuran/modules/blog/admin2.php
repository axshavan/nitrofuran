<?php

/**
 * Админская страница модуля blog
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

$te = new CTemplateEngine('blog');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/blog.php');
$te->assign('_left_menu', array
	(
		'index'    => array('active' => false, 'name' => 'Назад в админку', 'href' => '/admin/'),
		'bloglist' => array('active' => false, 'name' => 'Блоги',           'href' => '?page=bloglist')
	)
);
switch($_GET['page'])
{
	case 'blogadd':
	case 'blogedit':
	{
		require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');
		$te->assign('inner_template_name', 'a/blogform.tpl');
		$te->assign('userlist', CUser::GetList());
		$graph  = new CGraph();
		$res    = $DB->Query("select * from `".TREE_TABLE."` order by `pid` asc, `id` asc");
		$_vtree = array();
		while($r = $DB->Fetch($res))
		{
			$_vtree[$r['id']] = $r;
		}
		$graph->CreateFromArray($_vtree, 'pid');
		$te->assign('vtree', $graph->GetAsArray(true));
		if($_POST)
		{
			if($_POST['id'])
			{
				if(CBlog::Edit($_POST['id'], $_POST))
				{
					redirect('?page=bloglist');
					die();
				}
			}
			elseif(CBlog::Add($_POST))
			{
				redirect('?page=bloglist');
				die();
			}
			$te->assign('error',         true);
			$te->assign('id',            (int)$_POST['id']);
			$te->assign('name',          h($_POST['name']));
			$te->assign('userselected',  (int)$_POST['user_id']);
			$te->assign('vtreeselected', (int)$_POST['tree_id']);
		}
		elseif($_GET['id'])
		{
			$res = CBlog::GetList(array('id' => (int)$_GET['id']));
			$te->assign('name',          $res[0]['name']);
			$te->assign('userselected',  $res[0]['user_id']);
			$te->assign('vtreeselected', $res[0]['tree_id']);
			$te->assign('id',            $res[0]['id']);
			$te->assign('actionpage',    'blogedit');
		}
		else
		{
			$te->assign('name',         '');
			$te->assign('userselected',  0);
			$te->assign('vtreeselected', 0);
			$te->assign('actionpage',    'blogadd');
		}
		break;
	}
	case 'blogdelete':
	{
		CBlog::Delete($_REQUEST['id']);
		redirect('?page=bloglist');
		die();
		break;
	}
	case 'bloglist':
	default:
	{
		$te->_tpl_vars['_left_menu']['bloglist']['active'] = true;
		$te->assign('inner_template_name', 'a/bloglist.tpl');
		$te->assign('_blogs', CBlog::GetList());
		require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');
		$te->assign('userlist', CUser::GetList());
		$graph  = new CGraph();
		$res    = $DB->Query("select * from `".TREE_TABLE."` order by `pid` asc, `id` asc");
		$_vtree = array();
		while($r = $DB->Fetch($res))
		{
			$_vtree[$r['id']] = $r;
		}
		$graph->CreateFromArray($_vtree, 'pid');
		$te->assign('vtree', $graph->GetAsArray(true));
		$te->assign('userlist', CUser::GetList());
		break;
	}
}
$te->template('a/admin.tpl');

?>