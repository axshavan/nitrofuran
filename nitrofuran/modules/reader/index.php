<?php

/**
 * Главный файл модуля reader
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

require_once(dirname(__FILE__).'/config.php');
require_once(dirname(__FILE__).'/reader.php');
$reader    = new CReader();
$tplengine = new CTemplateEngine('reader');
$tplengine->assign('curpath', $_SERVER['REQUEST_URI']);

// подключение аяксового роутера
if(isset($_POST['ajax']))
{
	require(dirname(__FILE__).'/ajax.php');
	return;
}

$tplengine->template('index.tpl');

?>