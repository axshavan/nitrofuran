<?php

// stage 3

// возврат обратно на форму с ошибкой
function go_back_to_stage2($fail_module)
{
	global $stage;
	$stage = 2;
	$error_text = 'Не удалось установить модуль '.$fail_module;
	require_once(DOCUMENT_ROOT.'/nitrofuran/modules/install/stage2.php');
	die();
}

// поиск модулей, возможных для установки
$_modules = array();
$dir      = opendir(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules');
while($file = readdir($dir))
{
	if($file != '.' && $file != '..')
	{
		$module_name = explode('.', $file);
		$module_name = $module_name[0];
		$_modules[$module_name] = array(
			'file'     => $file,
			'name'     => $module_name,
			'istalled' => false
		);
	}
}

global $DB;
$DB = new CDatabase();
$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
foreach($_POST as $k => $v)
{
	$module = substr($k, 8);
	if(array_key_exists($module, $_modules))
	{
		if(file_exists(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/'.$_modules[$module]['file']))
		{
			ob_start();
			$result = require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/'.$_modules[$module]['file']);
			$_modules[$module]['output'] = ob_get_clean();
			if(!$result)
			{
				go_back_to_stage2($module);
			}
		}
		else
		{
			go_back_to_stage2($module);
		}
	}
}

$htaccess = explode("\n", file_get_contents(DOCUMENT_ROOT.'/.htaccess'));
foreach($htaccess as &$str)
{
	$str = ltrim($str, ' #');
}
file_put_contents(DOCUMENT_ROOT.'/.htaccess', implode("\n", $htaccess));

$DB->Disconnect();
redirect('/');

?>