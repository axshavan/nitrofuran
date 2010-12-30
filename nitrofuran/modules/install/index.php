<?php

/*
	Модуль установки движка. Он имеет несколько стадий (stages).
	- stage0 - определение, имеется ли подключение к БД, если нет, то
	  выводится форма для указания названия БД, имени пользователя и паролей
	  для их использования.
	- stage1 - создание БД, пользователя, если надо, правка конфига.
	- stage2 - вывод списка модулей, которые можно установить или
	  переустановить.
*/

global $stage;
$stage = (int)$_REQUEST['stage'];
if(file_exists(DOCUMENT_ROOT.'/nitrofuran/modules/install/stage'.$stage.'.php'))
{
	require(DOCUMENT_ROOT.'/nitrofuran/modules/install/stage'.$stage.'.php');
}
else
{
	$stage = 0;
	require(DOCUMENT_ROOT.'/nitrofuran/modules/install/stage0.php');
}

?>