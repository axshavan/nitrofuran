<?php

/**
 *
 * Подключение внешнего скрипта и внешних шаблонов для случая, когда
 * nitrofuran используется как фреймворк.
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/static/config.php');
global $TREE_INFO;
global $DB;

$tree_id = (int)$TREE_INFO['current']['id'];
$res     = $DB->QueryFetched("select `content` from `".STATIC_PAGES_TABLE."` where `tree_id` = '".$tree_id."'");
if(!$res[0])
{
	error404();
}
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/modules/static/static_router.php'))
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/modules/static/static_router.php');
}

?>