<?php

// stage 1

// возврат обратно на форму с ошибкой
function go_back_to_stage0($cid = false)
{
	$tplengine = new CTemplateEngine('install');
	$tplengine->assign('error_text',      mysql_errno($cid).': '.mysql_error($cid));
	$tplengine->assign('mysql_host',      $_POST['mysql_host']);
	$tplengine->assign('mysql_user',      $_POST['mysql_user']);
	$tplengine->assign('mysql_password',  $_POST['mysql_password']);
	$tplengine->assign('mysql_database',  $_POST['mysql_database']);
	$tplengine->assign('create_user',     $_POST['create_user'] ? true : false);
	$tplengine->assign('create_database', $_POST['create_database'] ? true : false);
	$tplengine->assign('rootpwd',         $_POST['rootpwd']);
	$tplengine->template('stage0.tpl');
	die();
}

// если надо создать базу или пользователя, коннектимся под рутом
if($_POST['create_user'] || $_POST['create_database'])
{
	$cid = mysql_connect($_POST['mysql_host'], 'root', $_POST['rootpwd'], true);
	if(!$cid)
	{
		go_back_to_stage0();
	}
	if($_POST['create_user'])
	{
		mysql_query("create user '".$_POST['mysql_user']."'@'%' identified by '".$_POST['mysql_password']."'", $cid);
		if(mysql_errno($cid))
		{
			go_back_to_stage0($cid);
		}
	}
	if($_POST['create_database'])
	{
		mysql_query("create database ".$_POST['mysql_database']." default character set utf8 default collate utf8_general_ci", $cid);
		if(mysql_errno($cid))
		{
			go_back_to_stage0($cid);
		}
		mysql_query("grant all privileges on ".$_POST['mysql_database'].".* to '".$_POST['mysql_user']."'@'%'");
		if(mysql_errno($cid))
		{
			go_back_to_stage0($cid);
		}
	}
	mysql_query("flush privileges", $cid);
	mysql_close($cid);
}

// поднятие дампа основных таблиц
$cid = mysql_connect($_POST['mysql_host'], $_POST['mysql_user'], $_POST['mysql_password']);
if(!$cid)
{
	go_back_to_stage0();
}
if(!mysql_select_db($_POST['mysql_database'], $cid))
{
	go_back_to_stage0();
}
$sql = explode(';', file_get_contents(DOCUMENT_ROOT.'/nitrofuran/modules/install/deploy.sql'));
mysql_query("begin", $cid);
foreach($sql as $query)
{
	if(strlen(trim($query)))
	{
		mysql_query($query, $cid);
		if(mysql_errno($cid))
		{
			mysql_query("rollback", $cid);
			go_back_to_stage0($cid);
		}
	}
}
mysql_query("commit", $cid);

// запись параметров в конфиг
$conf_text = file_get_contents(DOCUMENT_ROOT.'/nitrofuran/config.php-dist');
$conf_text = preg_replace('/define[\s]*\([\s]*(\'|\")MYSQL_HOST\1[\s]*,[\s]*(\'|\")([\w\d\:\_\-\/]+)\2/',   "define('MYSQL_HOST', '".$_POST['mysql_host']."'", $conf_text);
$conf_text = preg_replace('/define[\s]*\([\s]*(\'|\")MYSQL_USER\1[\s]*,[\s]*(\'|\")([\w\d\:\_\-\/]+)\2/',   "define('MYSQL_USER', '".$_POST['mysql_user']."'", $conf_text);
$conf_text = preg_replace('/define[\s]*\([\s]*(\'|\")MYSQL_PASSWORD\1[\s]*,[\s]*(\'|\")([\s\S]+)\2/U',      "define('MYSQL_PASSWORD', '".$_POST['mysql_password']."'", $conf_text);
$conf_text = preg_replace('/define[\s]*\([\s]*(\'|\")MYSQL_DATABASE\1[\s]*,[\s]*(\'|\")([\w\d\:\_\-]+)\2/', "define('MYSQL_DATABASE', '".$_POST['mysql_database']."'", $conf_text);
file_put_contents(DOCUMENT_ROOT.'/nitrofuran/config.php', $conf_text);

header("location: install.php?stage=2");

?>