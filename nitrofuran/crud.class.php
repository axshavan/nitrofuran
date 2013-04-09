<?php

/**
 * Базовый класс для описания моделей, умеющий делать
 * create, read, update, delete - c.r.u.d.
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

class CRUD
{
	/**
	 * Создать запись
	 * @param  string $table   название таблицы
	 * @param  array  $_fields массив значений добавляемого поля (field_name => string value[, ...])
	 * @return bool
	 */
	public function create($table, $_fields)
	{
		global $DB;
		$query1 = "insert into `".$DB->EscapeString($table)."` (";
		$query2 = "";
		foreach($_fields as $k => $v)
		{
			$query1 .= "`".$DB->EscapeString($k)."`,";
			$query2 .= "'".$DB->EscapeString($v)."',";
		}
		return $DB->Query(substr($query1, 0, strlen($query1) - 1).") values (".substr($query2, 0, strlen($query2) - 1).")");
	}

	/**
	 * Достать несколько записей из таблицы
	 * @param  string $table   название таблицы
	 * @param  array  $_filter массив с параметрами фильтрации (field_name => string/array value[, ...])
	 * @param  array  $_sort   массив с параметрами сортировки (field_name => asc|desc)
	 * @param  array  $_params массив с прочими параметрами (limit, offset)
	 * @return array
	 */
	public function read($table, $_filter = array(), $_sort = array(), $_params = array())
	{
		global $DB;
		$query = "select * from `".$DB->EscapeString($table)."` "
			.$this->strWhere($_filter)." "
			.$this->strOrder($_sort)." "
			.$this->strParams($_params);
		return $DB->QueryFetched($query);
	}

	/**
	 * Обновить несколько записей в соответствии с фильтром
	 * @param  string $table   название таблицы
	 * @param  array  $_filter массив с параметрами фильтрации (field_name => string/array value[, ...])
	 * @param  array  $_values массив с новыми значениями (field_name => string[, ...])
	 * @return int    количество изменённых записей
	 */
	public function update($table, $_filter, $_values)
	{
		if(!is_array($_values) || !sizeof($_values))
		{
			return 0;
		}
		global $DB;
		$query = array();
		foreach($_values as $k => $v)
		{
			$query[] = "`".$DB->EscapeString($k)."` = '".$DB->EscapeString($v)."'";
		}
		$query = "update `".$DB->EscapeString($table)."` set ".implode(",", $query)." ".$this->strWhere($_filter);
		$DB->Query($query);
		return $DB->AffectedRows();
	}

	/**
	 * Удалить записи из таблицы в соответствии с фильтром
	 * @param  string $table   название таблицы
	 * @param  array  $_filter массив с параметрами фильтрации (field_name => string/array value[, ...])
	 * @return int    количество удалённых записей
	 */
	public function delete($table, $_filter = array())
	{
		global $DB;
		$query = "delete from `".$DB->EscapeString($table)."` ".$this->strWhere($_filter);
		$DB->Query($query);
		return $DB->AffectedRows();
	}

	/**
	 * mysql_last_insert_id типа
	 * @return int
	 */
	public function id()
	{
		global $DB;
		return $DB->InsertedId();
	}

	// дальше идут служебные функции

	/**
	 * Сделать строку для запроса where из фильтра
	 * @param  array $_filter
	 * @return string
	 */
	protected function strWhere($_filter)
	{
		if(!is_array($_filter) || !sizeof($_filter))
		{
			return "";
		}
		global $DB;
		$query = "where 1=1";
		foreach($_filter as $k => $v)
		{
			$operand = '=';
			if(is_array($v))
			{
				$operand = 'in';
				if($k[0] == '!')
				{
					$k = substr($k, 1);
					$operand = 'not in';
				}
				foreach($v as &$vv)
				{
					$vv = $DB->EscapeString($vv);
				}
				$v = "('".implode("','", $v)."')";
			}
			else
			{
				if($k[0] == '!')
				{
					$k = substr($k, 1);
					$operand = '!=';
				}
				elseif($k[0] == '<' || $k[0] == '>')
				{
					$operand = $k[0];
					$k = substr($k, 1);
					if($k[0] == '=')
					{
						$k        = substr($k, 1);
						$operand .= '=';
					}
				}
				$v = "'".$DB->EscapeString($v)."'";
			}
			$query .= " and `".$DB->EscapeString($k)."` ".$operand." ".$v;
		}
		return $query;
	}

	/**
	 * Сделать строку для сортировки
	 * @param  array $_sort
	 * @return string
	 */
	protected function strOrder($_sort)
	{
		if(!is_array($_sort) || !sizeof($_sort))
		{
			return "";
		}
		global $DB;
		$query = array();
		foreach($_sort as $k => $v)
		{
			$query[] = "`".$DB->EscapeString($k)."` ".(strtoupper($v) == 'DESC' ? 'desc' : 'asc');
		}
		return "order by ".implode(",", $query);
	}

	/**
	 * Сделать строку с параметрами выборки
	 * @param  $_params
	 * @return string
	 */
	protected function strParams($_params)
	{
		if(!isset($_params['limit']))
		{
			return "";
		}
		return "limit ".(int)$_params['limit'].((int)$_params['offset'] ? " offset ".(int)$_params['offset'] : "");
	}
}

?>