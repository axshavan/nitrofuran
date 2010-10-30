<?php

/*
 * Установка модуля vtree
 */

global $DB;
$DB->TransactionStart();

// добавление vtree в параметр modules_installed админки
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['vtree'] = 'Пути и папки';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:5:"vtree";s:22:"Пути и папки";}');
}

// добавление параметров модуля vtree
new_param('vtree', 'admin_menu', 'Пункты админского меню', 'textarray', 'a:1:{i:1;s:46:"Виртуальное дерево папок";}');

$DB->TransactionCommit();

?>