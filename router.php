<?php

/**
 * Маршрутизатор движка nitrofuran.
 * Здесь происходит подключение всех необходимых файлов, создание глобальных
 * объектов, и так далее. Сюда должен вести rewrite.
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

// development environment
error_reporting(E_ALL);
ini_set('display_errors', 1);

if($_SERVER['REMOTE_ADDR'] == '127.0.0.1' && isset($_SERVER['HTTP_X_REAL_IP']))
{
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_REAL_IP'];
}
elseif($_SERVER['REMOTE_ADDR'] == '127.0.0.1' && isset($_SERVER['HTTP_X_FORWARDED_FOR']))
{
	$_SERVER['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
}

if(!file_exists('nitrofuran/config.php'))
{
	header('Content-Type: text/plain; charset=utf8');
	echo 'Не существует или не доступен файл nitrofuran/config.php. Убедитесь, что он существует и что веб-сервер имеет права на его чтение.';
	die();
}
require_once('nitrofuran/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/libfunc.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/db.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/auth.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/module.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/te.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/tracer.class.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/user.class.php');

global $DB;
$DB = new CDatabase();
$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

global $REQUEST_URI;
$REQUEST_URI = explode('?', $_SERVER['REQUEST_URI']);
$REQUEST_URI = trim($REQUEST_URI[0], '/');
if(strlen(HTTP_ROOT) && strpos($REQUEST_URI, trim(HTTP_ROOT, '/')) === 0)
{
	$REQUEST_URI = substr($REQUEST_URI, strlen(trim(HTTP_ROOT)));
}
if(strlen($REQUEST_URI))
{
	$REQUEST_URI = explode('/', $REQUEST_URI);
}
else
{
	$REQUEST_URI = array();
}

$sql_select = "
	t0.`id` as t0id,
	t0.`pid`      as t0pid,
	t0.`name`     as t0name,
	t0.`module`   as t0module,
	t0.`action`   as t0action,
	t0.`template` as t0template,
	t0.`access`   as t0access";
$sql_join   = "`".TREE_TABLE."` t0";
$sql_order  = "t0.`name` desc";
$max_i      = count($REQUEST_URI);
for($i = 1; $i <= $max_i; $i++)
{
	$sql_select .= ",
		t".$i.".`id`       as t".$i."id,
		t".$i.".`pid`      as t".$i."pid,
		t".$i.".`name`     as t".$i."name,
		t".$i.".`module`   as t".$i."module,
		t".$i.".`action`   as t".$i."action,
		t".$i.".`template` as t".$i."template,
		t".$i.".`access`   as t".$i."access";
	$sql_join   .= " join `".TREE_TABLE."` t".$i."
		on (
			(t".$i.".`name` = '".$DB->EscapeString($REQUEST_URI[$i - 1])."' or t".$i.".`name` = '*')
			and t".$i.".`pid` = t".($i - 1).".`id`
		)";
	$sql_order  .= ", t".$i.".`name` desc";
}
$sql = "select ".$sql_select." from ".$sql_join." where t0.`id` = 1 order by ".$sql_order;
$res = $DB->Query($sql);

$_fetch = $DB->Fetch($res);
if(!$_fetch)
{
	// неверный путь
	error404();
}

global $TREE_INFO;
$TREE_INFO = array();
for($i = 0; $i <= $max_i; $i++)
{
	$TREE_INFO[$i] = array(
		'id'             => $_fetch['t'.$i.'id'],
		'pid'            => $_fetch['t'.$i.'pid'],
		'module'         => $_fetch['t'.$i.'module'],
		'action'         => $_fetch['t'.$i.'action'],
		'template'       => $_fetch['t'.$i.'template'],
		'access'         => $_fetch['t'.$i.'access'],
		'name'           => $_fetch['t'.$i.'name'],
		'name_requested' => isset($REQUEST_URI[$i - 1]) ? $REQUEST_URI[$i - 1] : ''
	);
}
$i = $max_i;
$TREE_INFO['current'] = array(
	'id'             => $_fetch['t'.$i.'id'],
	'pid'            => $_fetch['t'.$i.'pid'],
	'module'         => $_fetch['t'.$i.'module'],
	'action'         => $_fetch['t'.$i.'action'],
	'template'       => $_fetch['t'.$i.'template'],
	'access'         => $_fetch['t'.$i.'access'],
	'name'           => $_fetch['t'.$i.'name'],
	'name_requested' => isset($REQUEST_URI[$i - 1]) ? $REQUEST_URI[$i - 1] : ''
);

global $AUTH;
$AUTH = new CAuth();
$AUTH->StartSession();

if($TREE_INFO['current']['module'])
{
	$error = '';
	if(!CModule::Module($TREE_INFO['current']['module'], $error))
	{
		switch($error)
		{
			case 'NO MODULE FILE':
			{
				header('Content-Type: text/plain; charset=utf8');
				echo 'Ошибка подключения модуля. Требуемый файл не найден.';
				die();
				break;
			}
			case 'ACCESS FORBIDDEN':
			{
				error403();
				break;
			}
			case '':
			default:
			{
				break;
			}
		}
	}
}
else
{
	// неверный путь
	error404();
}

$DB->Disconnect();

?>