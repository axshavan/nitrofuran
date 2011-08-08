<?php

// 000020
// установка модуля user

$_modules_installed = unserialize(get_param('admin', 'modules_installed'));
$_modules_installed['user'] = 'Пользователи';
set_param('admin', 'modules_installed', serialize($_modules_installed));
return new_param
(
	'user',
	'admin_menu',
	'Пункты админского меню',
	'textarray',
	serialize
	(
		array
		(
			1 => 'Список пользователей'
		)
	)
);
return true;

?>