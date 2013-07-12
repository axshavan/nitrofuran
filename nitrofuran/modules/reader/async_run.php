<?php

/*
 * Консольный скрипт для асинхронного обновления подписок.
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */


define('DOCUMENT_ROOT', dirname(__FILE__).'/../../..');
require_once(DOCUMENT_ROOT.'/nitrofuran/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/libfunc.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/db.class.php');
$DB = new CDatabase();
$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/config.php');

$res = $DB->QueryFetched("select * from `".READER_SUBSCRIPTION_TABLE."`
	where `last_update` < unix_timestamp() - 1800 or `last_update` is NULL
	order by `last_update` asc
	limit 6");
if(!sizeof($res))
{
	echo "No subscribtions to update\n";
	$DB->Disconnect();
	die();
}
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/reader/reader.php');
$reader                = new CReader();
$time_start            = microtime(true);
$updated_subscribtions = 0;
foreach($res as $subscription)
{
	// LJ bot policy: не более 5 запросов в секунду
	if($time_start > microtime(true) - 0.2)
	{
		usleep(200);
	}
	$
	$time_start = microtime(true);
	$reader->curlGetItems($subscription, $a);
	$DB->Query("update `".READER_SUBSCRIPTION_TABLE."` set `last_update` = unix_timestamp()
		where `id` = '".$subscription['id']."'");
	$updated_subscribtions++;
}
echo "Updated ".$updated_subscribtions." subscibtions\n";
$DB->Disconnect();

?>