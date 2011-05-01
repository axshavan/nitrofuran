<?php

/*
	Набор функций для удобства вызова модуля.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

class CModule
{
	/*
		Проверка, установлен ли модуль с таким именем.
		@param  string $module_name название модуля
		@return bool
	*/
	public static function IsModuleInstalled($module_name)
	{
		return array_key_exists($module_name, unserialize(get_param('admin', 'modules_installed')));
	}
	
	/*
		Вызвать модуль.
		@param  string $module_name имя модуля
		@param  string &$error код ошибки
		@return bool
	*/
	public static function Module($module_name, &$error)
	{
		global $TREE_INFO;
		global $AUTH;
		global $DB;
		$error = '';
		if(!$TREE_INFO['current']['access'])
		{
			$bForbidden = true;
			// надо проверить права доступа
			if($AUTH->user_data['id'] == 1)
			{
				// админу везде можно
				$bForbidden = false;
			}
			if($bForbidden)
			{
				// нет прав доступа
				$error = 'ACCESS FORBIDDEN';
				return false;
			}
		}
		$file = strlen($TREE_INFO['current']['action']) ? $TREE_INFO['current']['action'] : 'index';
		if($file == 'admin')
		{
			// сюда можно только через модуль admin
			error403();
		}
		if(file_exists(DOCUMENT_ROOT.'/nitrofuran/modules/'.$module_name.'/'.$file.'.php'))
		{
			require(DOCUMENT_ROOT.'/nitrofuran/modules/'.$module_name.'/'.$file.'.php');
			return true;
		}
		else
		{
			// нет входного файла модуля
			$error = 'NO MODULE FILE';
			return false;
		}
	}
}

?>