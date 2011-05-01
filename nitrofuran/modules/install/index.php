<?php

/*
	Модуль установки движка. Он имеет несколько стадий (stages).
	- stage0 - определение, имеется ли подключение к БД, если нет, то
	  выводится форма для указания названия БД, имени пользователя и паролей
	  для их использования.
	- stage1 - создание БД, пользователя, если надо, правка конфига.
	- stage2 - вывод списка модулей, которые можно установить или
	  переустановить.
	
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
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