<?php

// 000015
// добавление папок login и logout, сброс свободного доступа

global $DB;

$DB->Query("update `".TREE_TABLE."` set `access` = 0 where `pid` != 0");
$res  = $DB->Query("select `id` from `".TREE_TABLE."` where `pid` = 0");
$_row = $DB->Fetch($res);
$pid  = $_row['id'];
$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'login', 'auth', 'login', '', 1)");
$DB->Query("insert into `".TREE_TABLE."` (`pid`, `name`, `module`, `action`, `template`, `access`) values
	('".$pid."', 'logout', 'auth', 'logout', '', 1)");
return true;

?>