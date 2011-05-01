<?php

/*
	Входная точка для скрипта инсталляции. Основное действо творится в файле,
	который подключается последним по счёту.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

if(file_exists('nitrofuran/config.php'))
{
	require_once('nitrofuran/config.php');
}
else
{
	define('HTTP_ROOT',     ''); // размещение корня сайта с точки зрения сервера
	define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].HTTP_ROOT); // физическое размещение корня сайта
}
require_once(DOCUMENT_ROOT.'/nitrofuran/libfunc.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/db.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/te.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/tracer.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/install/index.php');

?>