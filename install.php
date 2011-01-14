<?php

/*
	Входная точка для скрипта инсталляции. Основное действо творится в файле,
	который подключается последним по счёту.
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