<?php

/*
 * Некотороые полезные вещи, которые могут быть использованы более, чем в одном
 * месте в коде модуля хранения паролей.
 */

/*
	Расшифровать строку, зашифрованную kokol_encode().
	@param  string $string зашифрованная строка
	@return string расшифрованная строка
*/
function kokol_decode($string)
{
	return (
		kokol_str_replace(
			str_split('0qwe9Q.,WE8rty7RTY6;uiop5(UIOP4asd3ASD2fgh1FGH!jkl@:J)KL#zxc$ZXC%vbn^VBN&m*M'),
			str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*():;,.'),
			base64_decode($string)
		)
	);
}

/*
	Маленько подзашифровать строку на всякий случай.
	@param  string $string зашифровываемая строка
	@return string зашифрованная строка
*/
function kokol_encode($string)
{
	return base64_encode(
		kokol_str_replace(
			str_split('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890!@#$%^&*():;,.'),
			str_split('0qwe9Q.,WE8rty7RTY6;uiop5(UIOP4asd3ASD2fgh1FGH!jkl@:J)KL#zxc$ZXC%vbn^VBN&m*M'),
			$string
		)
	);
}

/*
	Замена обычной функции str_replace, работа которой меня не устраивает.
	@param  array  $alphabet1 что заменять
	@param  array  $alphabet2 на что заменять
	@param  string $string    где заменять
	@return string строка с заменёнными символами
*/
function kokol_str_replace($alphabet1, $alphabet2, $string)
{
	$_replace = array_combine($alphabet1, $alphabet2);
	$result = '';
	$l      = strlen($string);
	for($i = 0; $i < $l; $i++)
	{
		$s = $_replace[$string[$i]];
		if(strlen($s))
		{
			$result .= $s;
		}
		else
		{
			$result .= $string[$i];
		}
	}
	return $result;
}

?>