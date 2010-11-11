<?php

/*

Страница администрирования обновлений

*/

function run_script($filename, &$error_text)
{
	global $DB;
	$error_text  = '';
	$script_type = explode('.', $filename);
	$script_type = strtolower($script_type[sizeof($script_type) - 1]);
	switch($script_type)
	{
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

if(isset($_GET['proceed']))
{
	$dirname = DOCUMENT_ROOT.'/nitrofuran/modules/update/updates/';
	$dir = opendir($dirname);
	if(!$dir)
	{
		$tplengine->assign('error_text', 'Невозможно открыть папку со скриптами апдейтов');
	}
	else
	{
		$_upd_scripts = array();
		$current_version = get_param('update', 'version');
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
		foreach($_upd_scripts as $v => $file)
		{
			if(!run_script($dirname.$file, &$error_text))
			{
				$error_text = 'Script '.$file.(strlen($error_text) ? ':<br>'.$error_text : '');
				$tplengine->assign('error_text', $error_text);
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