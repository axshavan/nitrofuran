<?php

/*

	Страница администрирования модуля виртуального дерева и статичных страниц.

*/

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');

$res    = $DB->Query("select * from `".TREE_TABLE."` order by `pid` asc, `id` asc");
$_vtree = array();
while($r = $DB->Fetch($res))
{
	$r['parent_id']   = $r['pid'];
	$_vtree[$r['id']] = $r;
}
$graph = new graph();
$graph->CreateFromArray($_vtree);
$graph->Trace(1);

die();
$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/vtree/templates/admin.tpl');

?>