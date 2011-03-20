<?php

/*
	Ошибка 403.
*/
function error403()
{
	header("HTTP/1.0 403 Forbidden");
	header("Status: 403 Forbidden");
	echo 'HTTP/1.0 403 Forbidden';
	die();
}

/*
	Ошибка 404.
*/
function error404()
{
	header("HTTP/1.0 404 Not Found");
	header("Status: 404 Not Found");
	echo 'HTTP/1.0 404 Not Found';
	die();
}

/*
	Получить значение параметра.
	@param  string $module     название модуля
	@param  string $param_name название параметра
	@return string
*/
function get_param($module, $param_name)
{
	global $DB;
	$_result = $DB->Fetch($DB->Query("select `value` from `".PARAMS_TABLE."` where `module` = '".$DB->EscapeString($module)."' and `name` = '".$DB->EscapeString($param_name)."'"));
	return $_result['value'];
}

/*
	htmlspecialchars писать слишком длинно.
	@param  string $s строка, которую надо обработать
	@return string
*/
function h($s)
{
	return htmlspecialchars($s);
}

/*
	Добавить новый параметр.
	@param string $module       название модуля
	@param string $param_name   название параметра
	@param string $display_name отображаемое название параметра
	@param string $type         тип нового параметра
	@param string $value        новое значение
*/
function new_param($module, $param_name, $display_name = '', $type = 'text', $value = '')
{
	global $DB;
	return $DB->Query("insert into `".PARAMS_TABLE."` (`module`, `name`, `display_name`, `type`, `value`) values (
		'".$DB->EscapeString($module)."',
		'".$DB->EscapeString($param_name)."',
		'".$DB->EscapeString($display_name)."',
		'".$DB->EscapeString($type)."',
		'".$DB->EscapeString($value)."')");
}

/*
	Сделать редирект, послав header(location).
	@param string $location куда редирект
*/
function redirect($location)
{
	ob_end_clean();
	header('location: '.$location);
}

/*
	Жалкая попытка русифицировать date().
	@param  string $format    формат даты, как в date()
	@param  int    $timestamp таймстамп
	@return string
*/
function rudate($format, $timestamp = false)
{
	if($timestamp === false)
	{
		$timestamp = time();
	}
	$result = date($format, $timestamp);
	$result = str_replace(
		array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'),
		array('Янв', 'Фев', 'Мар', 'Апр', 'Май', 'Июн', 'Июл', 'Авг', 'Сен', 'Окт', 'Ноя', 'Дек'),
		$result);
	$result = str_replace(
		array('Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'),
		array('Пн',  'Вт',  'Ср',  'Чт',  'Пт',  'Сб',  'Вс'),
		$result);
	return $result;
}

/*
	Установить значение параметра.
	@param string $module     название модуля
	@param string $param_name название параметра
	@param string $value      новое значение
*/
function set_param($module, $param_name, $value)
{
	global $DB;
	$res  = $DB->Query("select `value` from `".PARAMS_TABLE."` where `module` = '".$DB->EscapeString($module)."' and `name` = '".$DB->EscapeString($param_name)."'");
	$_row = $DB->Fetch($res);
	if($_row)
	{
		if($_row['value'] != $value)
		{
			$DB->Query("update `".PARAMS_TABLE."` set `value` = '".$DB->EscapeString($value)."' where `module` = '".$DB->EscapeString($module)."' and `name` = '".$DB->EscapeString($param_name)."'");
		}
	}
	else
	{
		new_param($module, $param_name, '', 'text', $value);
	}
}

/*
	Заменить значение одного из параметров в строке типа ...?param1=val1&param2=val2...
	@param  string $param_name     название параметра
	@param  string $new_value      новое значение
	@param  string $request_string исходная строка запроса (не обязательно)
	@return string
*/
function string_request_replace($param_name, $new_value, $request_string = '')
{
	if(!strlen($request_string))
	{
		$request_string = $_SERVER['REQUEST_URI'];
	}
	$request_string = explode('?', $request_string);
	$s = $request_string[1];
	$s = explode('&', $s);
	$bReplaced = false;
	foreach($s as &$v)
	{
		$v = explode('=', $v);
		if($v[0] == $param_name)
		{
			$v[1] = $new_value;
			$bReplaced = true;
		}
		$v = implode('=', $v);
	}
	$s = implode('&', $s);
	if(!$bReplaced)
	{
		$s = $param_name.'='.$new_value.(strlen($s) ? '&'.$s : '');
	}
	$request_string = $request_string[0].'?'.$s;
	return $request_string;
}

?>