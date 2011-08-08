<?php

/*
	Процессор шаблонов.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/
class CTemplateEngine
{
	public $_tpl_vars = array();
	public $module    = '';
	
	/*
		Конструктор.
		@param string $module_name имя модуля (где искать шаблоны)
	*/
	public function __construct($module_name = '')
	{
		$this->module = $module_name;
	}
	
	/*
		Деструктор.
	*/
	public function __destruct()
	{
	}
	
	/*
		Передать переменную в шаблон.
		@param string $var_name  имя переменной
		@param mixed  $var_value значение переменной
	*/
	public function assign($var_name, $var_value)
	{
		$this->_tpl_vars[$var_name] = $var_value;
	}
	
	/*
		Удалить переменную из шаблона
		@param string $var_name имя переменной
	*/
	public function deassign($var_name)
	{
		unset($this->_tpl_vars[$var_name]);
	}
	
	/*
		Отобразить шаблон.
		@param  string $tpl_name имя шаблона
		@return string код ошибки
	*/
	public function template($tpl_name)
	{
		if($tpl_name[0] != '/')
		{
			$tpl_name = DOCUMENT_ROOT.'/nitrofuran/modules/'.$this->module.'/templates/'.$tpl_name;
		}
		if(!file_exists($tpl_name))
		{
			return 'TEMPLATE DOES NOT EXIST';
		}
		unset($this->_tpl_vars['this']);
		extract($this->_tpl_vars);
		include($tpl_name);
		return true;
	}
	
	/*
		Вызвать любой метод любого класса из недр шаблона.
		@param  string $class_name  имя класса
		@param  string $method_name имя метода
		@param  ???    любое количество параметров, которые передадутся
			в вызываемый метод
		@return mixed  что-нибудь
	*/
	protected function CallClassMethod($class_name, $method_name)
	{
		if(class_exists($class_name))
		{
			$obj = new $class_name();
		}
		else
		{
			return 'CLASS DOES NOT EXIST';
		}
		if(method_exists($obj, $method_name))
		{
			$args = func_get_args();
			unset($args[0]);
			unset($args[1]);
			return call_user_func_array(array($obj, $method_name), $args);
		}
		else
		{
			return 'CLASS METHOS DOES NOT EXIST';
		}
	}
	
	/*
		Вызвать подшаблон из шаблона
		@param  string $tpl_name имя подшаблона
		@return string код ошибки
	*/
	protected function IncludeTemplate($tpl_name)
	{
		$tplengine = new CTemplateEngine();
		$tplengine->_tpl_vars = $this->_tpl_vars;
		return $tplengine->template($tpl_name);
	}
}

?>