<?php

/*
	Инструмент для отладки.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

class CTracer
{
	// внутренний счётчик div'ов
	protected static $div_counter = 0;
	
	/*
		Осуществить вывод информации о переменной.
		@param string $dump результат выполнения функции var_dump
		@param string $var_name опциональное имя переменой
	*/
	public static function trace($dump, $var_name = '')
	{
		return '<table cellpadding=3 cellspacing=0>'.CTracer::parse_dump($dump, $var_name).'</table>';
	}
	
	/*
		Непосредственный вывод переменной.
	*/
	protected static function draw_element($_desc, $name = '')
	{
		$tdstyle  = 'border: 1px solid black; ';
		$tdstyle2 = $tdstyle;
		if(!strlen($name))
		{
			$name = ' ';
		}
		switch($_desc[1])
		{
			case 'bool':
			{
				$tdstyle .= 'background-color: '
					.($_desc[2] == 'true' ? 'white' : 'black').'; color: '
					.($_desc[2] == 'true' ? 'black' : 'white').';';
				$tdstyle2 = $tdstyle;
				$type     = $_desc[1];
				$value    = $_desc[2];
				$bFold    = false;
				break;
			}
			case 'int':
			{
				$tdstyle  .= 'background-color: #ff0000; color: white;';
				$tdstyle2 .= 'background-color: #ff8080; color: black;';
				$type      = $_desc[1];
				$value     = $_desc[2];
				$bFold     = false;
				break;
			}
			case 'float':
			{
				$tdstyle  .= 'background-color: #ff00ff; color: black;';
				$tdstyle2 .= 'background-color: #ff80ff; color: black;';
				$type      = $_desc[1];
				$value     = $_desc[2];
				$bFold     = false;
				break;
			}
			case 'string':
			{
				$tdstyle  .= 'background-color: #0000ff; color: white;';
				$tdstyle2 .= 'background-color: #8080ff; color: black;';
				$type      = $_desc[1].'('.$_desc[2].')';
				$value     = nl2br($_desc[4]);
				$bFold     = false;
				break;
			}
			case 'array':
			{
				$tdstyle  .= 'background-color: #008000; color: black;';
				$tdstyle2 .= 'background-color: #60a060; color: black;';
				$type      = $_desc[1].'('.$_desc[2].')';
				$value     = '';
				$bFold     = true;
				break;
			}
			case 'resource':
			{
				$tdstyle  .= 'background-color: #ff8000; color: black;';
				$tdstyle2 .= 'background-color: #ffa040; color: black;';
				$type      = $_desc[1];
				$value     = $_desc[5];
				$bFold     = false;
				break;
			}
			case 'object':
			{
				$tdstyle  .= 'background-color: #00ff00; color: black;';
				$tdstyle2 .= 'background-color: #80ff00; color: black;';
				$type      = $_desc[1].'('.$_desc[2].')';
				$value     = '';
				$bFold     = true;
				break;
			}
			case 'recursion':
			{
				$tdstyle  .= 'background-color: #0080ff; color: black;';
				$tdstyle2 .= 'background-color: white; color: black;';
				$type      = $_desc[1];
				$value     = '';
				break;
			}
			case 'NULL':
			default:
			{
				$tdstyle .= 'background-color: #a0a0a0; color: #606060;';
				$tdstyle2 = $tdstyle;
				$type     = $_desc[1];
				$value    = '';
				$bFold    = false;
			}
		}
		$value_div_id = 'tracer_div_'.CTracer::$div_counter;
		CTracer::$div_counter++;
		$value =
			($bFold ?
				'<span style="cursor: pointer; color: #404040; font-size: smaller;" onclick="document.getElementById(\''.$value_div_id.'\').style.display=\'none\';">[-]</span> '
				.'<span style="cursor: pointer; color: #404040; font-size: smaller;" onclick="document.getElementById(\''.$value_div_id.'\').style.display=\'block\';document.getElementById(\''.$value_div_id.'\').style.maxHeight=\'\';document.getElementById(\''.$value_div_id.'\').style.overflow=\'\';">[+]</span> '
				.'<span style="cursor: pointer; color: #404040; font-size: smaller;" onclick="document.getElementById(\''.$value_div_id.'\').style.display=\'block\';document.getElementById(\''.$value_div_id.'\').style.maxHeight=\'200px\';document.getElementById(\''.$value_div_id.'\').style.overflowY=\'scroll\';">[o]</span>'
			: '')
			.'<div id="'.$value_div_id.'">'.$value;
		$tdstyle .= '; vertical-align: top;';
		return '<tr><td style="'.$tdstyle.'">'.$name.'&nbsp;</td>'
			.'<td style="'.$tdstyle.'">'.$type.'</td>'
			.'<td style="'.$tdstyle2.'">'.$value;
	}
	
	/*
		Сюда передаётся результат выполнения var_dump или его часть для
		последующего вывода.
	*/
	protected static function parse_dump($dump, $var_name)
	{
		if(trim($dump) == 'NULL')
		{
			$_desc = array(1 => 'NULL');
		}
		else
		{
			$_text = explode("\n", $dump);
			$_desc = array();
			if(trim($_text[0]) == '*RECURSION*')
			{
				$_desc = array(1 => 'recursion');
			}
			else
			{
				preg_match('/([\w]+)\(([\w\.\-]+)\)(\s\"([\s\S]*)\"|\s\{|\#[\d]+\s\([\d]+\)\s\{|\sof\stype\s\(([\w\s]+)\)|)/', $_text[0], $_desc);
			}
			switch($_desc[1])
			{
				case 'string':
				{
					if((!strlen($_desc[3]) || !strlen($_desc[4])) && $_desc[2] > 0)
					{
						$string = substr(ltrim($_text[0]), 10 + strlen($_desc[2]));
						$line = 1;
						while(strlen($string) < $_desc[2])
						{
							$string .= "\n".$_text[$line];
							unset($_text[$line]);
							$line++;
						}
						$_desc[4] = rtrim($string, '"');
						$_desc[3] = ' "'.$_desc[4].'"';
					}
					break;
				}
				default:
				{
					break;
				}
			}
		}
		$result = CTracer::draw_element($_desc, $var_name);
		if(isset($_desc[0]) && $_desc[0][strlen($_desc[0]) - 1] == '{')
		{
			array_pop($_text);
			array_shift($_text);
			$result .= CTracer::parse_elements($_text);
		}
		return $result.' ';
	}
	
	/*
		Разбор элементов или вложенных переменных переменной.
	*/
	protected static function parse_elements($_elements)
	{
		if(!count($_elements))
		{
			return 'empty';
		}
		$level = strlen($_elements[0]) - strlen(ltrim($_elements[0]));
		$_desc = array();
		preg_match('/\[(\"|)([\S\s]+)\1\]/', $_elements[0], $_desc);
		$result = '<table cellpadding=3 cellspacing=0>';
		$name   = $_desc[2];
		$_dump  = array();
		for($i = 1; $i < count($_elements); $i++)
		{
			$element_level = strlen($_elements[$i]) - strlen(ltrim($_elements[$i]));
			if(($element_level == $level && strpos(ltrim($_elements[$i]), '[') !== 0) || ($element_level > $level))
			{
				$_dump[] = $_elements[$i];
			}
			elseif($element_level == $level && strpos(ltrim($_elements[$i]), '[') === 0)
			{
				$result .= CTracer::parse_dump(implode("\n", $_dump), $_desc[2]);
				$_dump   = array();
				preg_match('/\[(\"|)([\S\s]+)\1\]/', $_elements[$i], $_desc);
			}
			elseif($element_level == 0)
			{
				$_dump[count($_dump) - 1] .= "\n".$_elements[$i];
			}
		}
		$result .= CTracer::parse_dump(implode("\n", $_dump), $_desc[2]);
		$result .= '</table></div>';
		return $result;
	}
}

/*
	Красиво отобразить какую-нибудь переменную.
	@param mixed $var какая-нибудь переменная
	@param bool $return если true, то вернёт текст, если false, вывалит его
*/
function trace($var, $return = false)
{
	if(!headers_sent())
	{
		header("Content-Type: text/html; charset=utf-8");
	}
	ob_start();
	var_dump($var);
	$raw = trim(ob_get_clean());
	if($return)
	{
		return CTracer::trace($raw);
	}
	else
	{
		echo CTracer::trace($raw);
		return true;
	}
}

/*
	То же самое, что trace($var); die();, только покороче.
*/
function traced($var)
{
	trace($var);
	die();
}

?>