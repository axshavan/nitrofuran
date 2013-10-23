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

// переваривание res[meta]
$res['~meta']       = array();
$_allowed_languages = '';
foreach($res['meta'] as $_meta)
{
	if($_meta['key'] == 'allowed_languages')
	{
		$_allowed_languages = $_meta['value'];
	}
	$res['~meta'][$_meta['key']] = $_meta['value'];
}
$_allowed_languages = explode(',', $_allowed_languages);

// определение текущего языка
$language = '';
foreach($_allowed_languages as $l)
{
	if(strlen($l) && strpos($_SERVER['HTTP_ACCEPT_LANGUAGE'], $l) !== false)
	{
		$language = $l;
		break;
	}
}
if(!$language)
{
	$language = $_allowed_languages[0];
}

if($TREE_INFO['current']['template'] && file_exists(dirname(__FILE__).'/templates/'.$language.'_'.$TREE_INFO['current']['template']))
{
	$TREE_INFO['current']['template'] = $language.'_'.$TREE_INFO['current']['template'];
}
if($TREE_INFO['current']['template'] && file_exists(dirname(__FILE__).'/templates/'.$TREE_INFO['current']['template']))
{
	$tplengine = new CTemplateEngine('static');
	$tplengine->assign('_page', $res);
	$tplengine->assign('language', $language);
	$tplengine->template(dirname(__FILE__).'/templates/'.$TREE_INFO['current']['template']);
}
else
{
	error404();
}

?>