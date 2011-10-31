<?php

// 000021
// добавление пункта меню в модуле user

if(!CModule::IsModuleInstalled('user'))
{
	// касса не установлена
	return true;
}

$_params = unserialize(get_param('user', 'admin_menu'));
$_params[] = 'Новый пользователь';
set_param('user', 'admin_menu', serialize($_params));

return true;

?>