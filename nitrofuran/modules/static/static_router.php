<?php

/**
 * Пример файла, который может находиться на стороне сайта, работающего
 * с nitrofuran как с фреймворком и принимать вызовы для шаблона external
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

$content = $res[0]['content'];
if($TREE_INFO['current']['template'] && file_exists(dirname(__FILE__).'/templates/'.$TREE_INFO['current']['template']))
{
	require_once(dirname(__FILE__).'/templates/'.$TREE_INFO['current']['template']);
}
else
{
	error404();
}

?>