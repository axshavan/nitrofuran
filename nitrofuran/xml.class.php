<?php

/**
 * Парсер XML.
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

class CXMLParser
{
	// защищённое хранилище
	protected $storage;
	
	// конструктор
	public function __construct() {}
	
	// деструктор
	public function __desctruct()
	{
		unset($this->storage);
	}

	/**
	 * Получить содержимое хранилища
	 * @return array
	 */
	public function get_as_array()
	{
		return $this->storage;
	}

	/**
	 * Загрузить из файла
	 * @param string $file_name название файла
	 */
	public function load_from_file($file_name)
	{
		$this->load_from_string(file_get_contents($file_name));
	}

	/**
	 * Загрузить из строки
	 * @param string $xml_string XML в строке
	 */
	public function load_from_string(&$xml_string)
	{
		// очистить хранилище
		$this->storage = array();
		
		// отрезать заголовок xml
		$a = mb_stripos($xml_string, '?>', 0, 'utf-8');
		if($a)
		{
			$a += 2;
			$xml_string = mb_substr($xml_string, $a, mb_strlen($xml_string) - $a, 'utf-8');
		}
		$xml_string = str_replace("\n", ' ',   $xml_string);
		$xml_string = str_replace("\r", ' ',   $xml_string);
		$xml_string = str_replace("\t", ' ',   $xml_string);
		$xml_string = str_replace(">",  '> ',  $xml_string);
		$xml_string = str_replace("/>", '/> ', $xml_string);

		// передать парсеру
		$this->storage = $this->tags_regexp($xml_string);
	}

	/**
	 * Распарсить строку свойств тэга
	 * @param  string $prop_string строка свойст тэга
	 * @return array
	 */
	protected function parse_properties(&$prop_string)
	{
		$_result = array();
		$prop_string = explode(' ', trim($prop_string));
		foreach($prop_string as $v)
		{
			if($v)
			{
				$v = explode('=', $v);
				$_result[$v[0]] = trim($v[1], '"');
			}
		}
		return $_result;
	}

	/**
	 * Рекурсивно распарсить xml-строку регулярными выражениями
	 * @param string $xml_string XML строка, которую надо распарсить
	 * @return array|string
	 */
	protected function tags_regexp(&$xml_string)
	{
		$_result = array();
		while(true)
		{
			$xml_string = trim($xml_string).' ';
			$tagname = mb_substr($xml_string, 0, mb_stripos($xml_string, ' ', 0, 'utf-8'), 'utf-8');
			$tagname = trim($tagname, '<>/ ');
			if(!$tagname)
			{
				return trim($xml_string);
			}
			$tagname = str_replace('-', '\-', $tagname);
			$tagname = str_replace(':', '\:', $tagname);
			// регулярное выражение для <tag props/>
			preg_match('/^\<'.$tagname.'([^\>]*)\/\>/u', $xml_string, $_m);
			if(!$_m[0])
			{
				// регулярное выражение для <tag props>content</tag>
				preg_match('/^\<'.$tagname.'([^\>]*)\>([\s\S]*)\<\/'.$tagname.'\>/u', $xml_string, $_m);
			}
			if(!$_m[0])
			{
				return trim($xml_string);
			}
			$_result[] = array
			(
				'tag'        => stripslashes($tagname),
				'properties' => $this->parse_properties($_m[1]),
				'content'    => $this->tags_regexp($_m[2])
			);
			$mbstrlenm0 = mb_strlen($_m[0], 'utf-8');
			unset($_m);
			$xml_string = trim(mb_substr($xml_string, $mbstrlenm0, mb_strlen($xml_string, 'utf-8') - $mbstrlenm0, 'utf-8'));
			if(!mb_strlen($xml_string, 'utf-8'))
			{
				break;
			}
		}
		return $_result;
	}
}

?>