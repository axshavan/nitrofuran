<?php

/*
	Модуль администрирования всего.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

$template_name = 'index.tpl';
$tplengine     = new CTemplateEngine('admin');
$module        = $DB->EscapeString($_GET['module']);
if(!$module)
{
	$module = 'admin';
}
$tplengine->assign('module', $module);

$_modules_installed = unserialize(get_param('admin', 'modules_installed'));
foreach($_modules_installed as $k => $v)
{
	$_left_menu[] = array(
		'name'   => $v,
		'module' => $k,
		'active' => $module == $k
	);
}
$tplengine->assign("_modules_installed", $_modules_installed);
$tplengine->assign("_left_menu",         $_left_menu);
$_module_menu_param = unserialize(get_param($module, 'admin_menu'));
$_module_menu = array(
	array(
		'name'   => 'Параметры модуля',
		'page'   => '',
		'active' => (string)$page === ''
	)
);
foreach($_module_menu_param as $k => $v)
{
	$_module_menu[] = array(
		'name'   => $v,
		'page'   => $k,
		'active' => (string)$page === (string)$k
	);
}
$tplengine->assign('_module_menu', $_module_menu);

// подключение админской страницы другого модуля
$page = $DB->EscapeString($_GET['page']);
if($page)
{
	if(file_exists(DOCUMENT_ROOT.'/nitrofuran/modules/'.$module.'/admin.php'))
	{
		$tplengine->assign('page', $page);
		require(DOCUMENT_ROOT.'/nitrofuran/modules/'.$module.'/admin.php');
	}
	else
	{
		unset($page);
	}
}

// вывод обычной админской страницы
if(!$page)
{
	$res      = $DB->Query("select * from `".PARAMS_TABLE."` where `module` = '".$module."'");
	$_options = array();
	while($_option = $DB->Fetch($res))
	{
		$_options[$_option['name']] = $_option;
	}
	
	// сохранение изменений
	if($_POST)
	{
		foreach($_options as &$_option)
		{
			// обработка параметров в зависимости от их типов
			switch($_option['type'])
			{
				// массив
				case 'textarray':
				{
					$strlen_key   = strlen($_option['name'].'_key_');
					$strlen_value = strlen($_option['name'].'_value_');
					$_keys        = array();
					$_values      = array();
					foreach($_POST['o'] as $k => $v)
					{
						if(strpos($k, $_option['name'].'_key_') === 0)
						{
							$_keys[(int)substr($k, $strlen_key)] = $DB->EscapeString($v);
						}
						elseif(strpos($k, $_option['name'].'_value_') === 0 && $v)
						{
							$_values[(int)substr($k, $strlen_value)] = $DB->EscapeString($v);
						}
					}
					$_new_value = array();
					foreach($_values as $k => $v)
					{
						if($_keys[$k])
						{
							$_new_value[$_keys[$k]] = $_values[$k];
						}
						else
						{
							$_new_value[] = $_values[$k];
						}
					}
					set_param($module, $_option['name'], serialize($_new_value));
					break;
				}
				// чекбокс
				case 'checkbox':
				{
					if($_POST['o'][$_option['name']])
					{
						set_param($module, $_option['name'], '1');
					}
					else
					{
						set_param($module, $_option['name'], '0');
					}
					break;
				}
				// остальные
				case 'textarea':
				case 'text':
				default:
				{
					set_param($module, $_option['name'], $_POST['o'][$_option['name']]);
					break;
				}
			}
		}
		// новый параметр
		if(strlen($_POST['new_param_name']))
		{
			new_param($module, $_POST['new_param_name'], $_POST['new_param_display_name'], $_POST['new_param_type']);
		}
		header("location: ".$_SERVER['REQUEST_URI']);
	}
	$tplengine->assign('_options',            $_options);
	$tplengine->assign('inner_template_name', '');
}

$_left_menu = array(
	array(
		'name'   => 'Основные настройки',
		'module' => '',
		'active' => strlen($_GET['module']) ? false : true
	)
);
$_modules_installed = unserialize(get_param('admin', 'modules_installed'));
foreach($_modules_installed as $k => $v)
{
	$_left_menu[] = array(
		'name'   => $v,
		'module' => $k,
		'active' => $module == $k
	);
}
$tplengine->assign("_modules_installed", $_modules_installed);
$tplengine->assign("_left_menu",         $_left_menu);
$_module_menu_param = unserialize(get_param($module, 'admin_menu'));
$_module_menu = array(
	array(
		'name'   => 'Параметры модуля',
		'page'   => '',
		'active' => (string)$page === ''
	)
);
foreach($_module_menu_param as $k => $v)
{
	$_module_menu[] = array(
		'name'   => $v,
		'page'   => $k,
		'active' => (string)$page === (string)$k
	);
}
$tplengine->assign('_module_menu', $_module_menu);

$tplengine->template($template_name);

?>