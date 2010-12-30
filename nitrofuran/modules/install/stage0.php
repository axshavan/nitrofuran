<?php

// stage0

// тест подключения к БД
$cid = mysql_connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, true);
if($cid)
{
	do
	{
		// тест выбора БД
		if(!mysql_select_db(MYSQL_DATABASE, $cid))
		{
			break;
		}
		// БД, как указано в конфиге, существует, надо посмотреть, есть ли необходимые таблицы
		$res     = mysql_query('show tables');
		$_tables = array();
		while($_row = mysql_fetch_row($res))
		{
			$_tables[] = $_row[0];
		}
		if(!(
			in_array(TREE_TABLE,     $_tables) &&
			in_array(USERS_TABLE,    $_tables) &&
			in_array(SESSIONS_TABLE, $_tables) &&
			in_array(PARAMS_TABLE,   $_tables)
		))
		{
			break;
		}
		// вроде все необходимые таблицы есть, наверное, уже установлено
		$stage = 2;
		redirect('/install.php?stage=2');
		break;
	}
	while(false);
}
if(!$stage)
{
	$tplengine = new template_engine('install');
	$tplengine->template('stage0.tpl');
}

?>