<?php

/*
	Входная точка для скрипта инсталляции. Основное действо творится в файле,
	который подключается последним по счёту.
*/
require_once('nitrofuran/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/libfunc.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/db.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/te.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/install/index.php');

?>