<?php

/*
	Модуль статических страниц.
*/

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/static/config.php');
global $TREE_INFO;
global $DB;
$tree_id = (int)$TREE_INFO['current']['id'];
$res = $DB->Query("select `content` from `".STATIC_PAGES_TABLE."` where `tree_id` = '".$tree_id."'");
$res = $DB->Fetch($res);
if(!$res)
{
	error404();
}
echo $res['content'];

?>