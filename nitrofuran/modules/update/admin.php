<?php

/*
	Страница администрирования обновлений
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

/*
	Запуск скрипта апдейта.
	@param  string $filename        имя исполняемого файла
	@param  string &$error_text тут возвращается текст ошибки
	@return bool
*/
function run_script($filename, &$error_text)
{
	global $DB;
	$error_text  = '';
	$script_type = explode('.', $filename);
	$script_type = strtolower($script_type[sizeof($script_type) - 1]);
	switch($script_type)
	{
		// SQL-скрипт
		case 'sql':
		{
			$_sql = explode(';', file_get_contents($filename));
			$DB->TransactionStart();
			foreach($_sql as $query)
			{
				if(!$DB->Query($query))
				{
					$error_text = $DB->Error();
					$DB->TransactionRollback();
					return false;
				}
			}
			$DB->TransactionCommit();
			break;
		}
		// PHP-скрипт
		case 'php':
		default:
		{
			$result = require($filename);
			if($result === false)
			{
				return false;
			}
			else
			{
				return true;
			}
		}
	}
}

if(!strlen($_SERVER['DOCUMENT_ROOT']))
{
	// файл запущен из консоли
	$bConsoleRun = true;
	$nf_dir = explode('/', $_SERVER['PHP_SELF']);
	unset($nf_dir[sizeof($nf_dir) - 1]);
	unset($nf_dir[sizeof($nf_dir) - 1]);
	unset($nf_dir[sizeof($nf_dir) - 1]);
	$nf_dir = implode('/', $nf_dir);
	require_once($nf_dir.'/config.php');
	require_once($nf_dir.'/libfunc.php');
	require_once($nf_dir.'/db.class.php');
	require_once($nf_dir.'/auth.class.php');
	require_once($nf_dir.'/module.class.php');
	require_once($nf_dir.'/te.class.php');
	require_once($nf_dir.'/user.class.php');
	$tplengine = new CTemplateEngine();
	$DB = new CDatabase();
	$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
}
else
{
	// файл запущен из админки
	$bConsoleRun = false;
}

if(isset($_GET['proceed']) || $bConsoleRun)
{
	$dirname = DOCUMENT_ROOT.'/nitrofuran/modules/update/updates/';
	if($bConsoleRun)
	{
		$dirname = $nf_dir.'/..'.$dirname;
	}
	// посмотреть, какие апдейты есть
	$dir = opendir($dirname);
	if(!$dir)
	{
		if($bConsoleRun)
		{
			$DB->Disconnect();
			echo "Невозможно открыть папку со скриптами апдейтов\n";
			die();
		}
		else
		{
			$tplengine->assign('error_text', 'Невозможно открыть папку со скриптами апдейтов');
		}
	}
	else
	{
		$_upd_scripts = array();
		$current_version = get_param('update', 'version');
		// выбрать необходимые апдейты
		while($file = readdir($dir))
		{
			if($file != '.' && $file != '..')
			{
				$v = explode('.', $file);
				$v = (int)ltrim($v[0]);
				if($v > $current_version)
				{
					$_upd_scripts[$v] = $file;
				}
			}
		}
		closedir($dir);
		ksort($_upd_scripts);
		$bSuccess = true;
		// выполнить скрипты
		foreach($_upd_scripts as $v => $file)
		{
			if(!run_script($dirname.$file, &$error_text))
			{
				$error_text = 'Script '.$file.(strlen($error_text) ? ':<br>'.$error_text : '');
				if($bConsoleRun)
				{
					echo $error_text."\n";
					$DB->Disconnect();
					die();
				}
				else
				{
					$tplengine->assign('error_text', $error_text);
				}
				$bSuccess = false;
				break;
			}
			else
			{
				set_param('update', 'version',     $v);
				set_param('update', 'last_update', date('Y-m-d H:i:s'));
			}
		}
	}
	if($bSuccess)
	{
		$tplengine->assign('success_text', 'Обновление успешно завершено');
	}
	if($bConsoleRun)
	{
		echo "Обновление успешно завершено\n";
		$DB->Disconnect();
		die();
	}
}
$dir = opendir(DOCUMENT_ROOT.'/nitrofuran/modules/update/updates/');
if(!$dir)
{
	$tplengine->assign('error_text', 'Невозможно открыть папку со скриптами апдейтов');
}
else
{
	$available_version = 0;
	while($file = readdir($dir))
	{
		if($file != '.' && $file != '..')
		{
			$file    = explode('.', $file);
			$file[0] = (int)ltrim($file[0], '0');
			if($file[0] > $available_version)
			{
				$available_version = $file[0];
			}
		}
	}
	if(!$available_version)
	{
		$tplengine->assign('error_text', 'Скрипты апдейтов не найдены');
	}
	closedir($dir);
}
$tplengine->assign('version',             get_param('update', 'version'));
$tplengine->assign('last_update',         get_param('update', 'last_update'));
$tplengine->assign('available_version',   $available_version);
$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/update/templates/admin.tpl');

?>