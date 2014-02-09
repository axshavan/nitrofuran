<?

/**
 * Установка модуля stamps
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

// создание таблиц
if(!$DB->Query("create table ".STAMPS_TABLE." (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) DEFAULT NULL,
    `country` varchar(255) DEFAULT NULL,
    `year` varchar(10) DEFAULT NULL,
    `nominal` varchar(50) DEFAULT NULL,
    `slaked` int(11) NOT NULL DEFAULT '0',
    `book_id` int(11) NOT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM"))
{
    $DB->TransactionRollback();
    return false;
}
if(!$DB->Query("create table ".STAMPS_BOOKS_TABLE." (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) DEFAULT NULL,
    PRIMARY KEY (`id`)
    ) ENGINE=MyISAM"))
{
    $DB->TransactionRollback();
    return false;
}

// добавление в список модулей
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(is_array($r))
{
    $r['stamps'] = 'Марки';
    $r = serialize($r);
    set_param('admin', 'modules_installed', $r);
}
else
{
    new_param('admin', 'modules_installed', 'Установленные модули', 'textarray', 'a:1:{s:6:"stamps";s:10:"Марки";}');
}

// добавление путей в дерево папок
$r   = $DB->Query("select `id` from `".TREE_TABLE."` where `pid` = 0"); // по идее, это всегда 1, но мало ли...
$pid = $DB->Fetch($r);
$pid = $pid['id'];
if(!$pid)
{
    $DB->TransactionRollback();
    return false;
}
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'stamps', 'stamps', '', '', 0)");
$pid = $DB->InsertedId();
if(!$pid)
{
    $DB->TransactionRollback();
    return false;
}
$DB->Query("insert into `".TREE_TABLE."`
	(`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'import', 'stamps', 'import', '', 0)");

$DB->TransactionCommit();
return true;

?>