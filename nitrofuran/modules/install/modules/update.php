<?php

/*
 * Установка модуля update
 */

global $DB;
$DB->TransactionStart();

// добавление update в параметр modules_installed админки
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['update'] = 'Обновление';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:6:"update";s:20:"Обновление";}');
}

// добавление параметров модуля update
new_param('update', 'admin_menu',  'Пункты админского меню',    'textarray', 'a:1:{i:1;s:20:"Обновление";}');
new_param('update', 'version',     'Версия таблиц',             'text',      '0');
new_param('update', 'last_update', 'Последняя дата обновления', 'text',      '');

$DB->TransactionCommit();

?>