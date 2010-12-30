<?php

/*
 * Набор функций для удобства вызова модуля.
 */

class CModule
{
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