<?

/**
 * Установка модуля blog
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/stamps/config.php');
$DB->TransactionStart();

// добавление в список модулей
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
	$r['blog'] = 'Блог';
	$r = serialize($r);
	set_param('admin', 'modules_installed', $r);
}
else
{
	new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:4:"blog";s:8:"Блог";}');
}

// создание таблиц, добавление путей в дерево папок - этого всего здесь нет

?>