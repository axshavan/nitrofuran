<?php

/*
	Главная страница модуля для хранения параметров доступа к разным местам.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kokol/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kokol/lib.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');
global $DB;

$_categories = array();
$res = $DB->Query("select * from `".KOKOL_CATEGORIES_TABLE."`");
while($_r = $DB->Fetch($res))
{
	$_r['name'] = kokol_decode($_r['name']);
	$_categories[$_r['id']] = $_r;
}

$res = $DB->Query("select * from `".KOKOL_PASSWORDS_TABLE."`");
while($_r = $DB->Fetch($res))
{
	$_r['resource'] = kokol_decode($_r['resource']);
	$_r['login']    = kokol_decode($_r['login']);
	$_r['password'] = kokol_decode($_r['password']);
	$_categories[$_r['category_id']]['passwords'][] = $_r;
}

$graph = new CGraph();
$graph->CreateFromArray($_categories, 'pid');

$tplengine = new CTemplateEngine('kokol');
$tplengine->assign('_data', $graph->GetAsArray(true));
$tplengine->template('index.tpl');

?>