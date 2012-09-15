<?php

/*
	Пример того, как можно организовать работу статичных страниц,
	привнеся в неё немного динамики.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/static/config.php');
global $TREE_INFO;
global $DB;
$tplengine = new CTemplateEngine('static');
$tplengine->assign('title', $TREE_INFO['current']['name']);

$tree_id = (int)$TREE_INFO['current']['id'];
$res     = $DB->QueryFetched("select `content` from `".STATIC_PAGES_TABLE."` where `tree_id` = '".$tree_id."'");
if(!$res[0])
{
	error404();
}
$tplengine->assign('content', $res[0]['content']);
$tplengine->template($TREE_INFO['current']['template']);

?>