<?php

// stage 2

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

// уже установленные модули
global $DB;
$DB = new CDatabase();
$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
$_modules_installed = array_keys(unserialize(get_param('admin', 'modules_installed')));
foreach($_modules_installed as $module)
{
	$_modules[$module]['installed'] = true;
}

$tplengine = new CTemplateEngine('install');
$tplengine->assign('_modules', $_modules);
if($error_text)
{
	$tplengine->assign('error_text', $error_text);
}
$tplengine->template('stage2.tpl');
$DB->Disconnect();

?>