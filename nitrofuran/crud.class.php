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
		$query1 = "insert into `".$table."` (";
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
	public function read($table, $_filter, $_sort, $_params)
	{
		// ...
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
		// ...
	}

	/**
	 * Удалить записи из таблицы в соответствии с фильтром
	 * @param  string $table   название таблицы
	 * @param  array  $_filter массив с параметрами фильтрации (field_name => string/array value[, ...])
	 * @return int    количество удалённых записей
	 */
	public function delete($table, $_filter)
	{
		// ...
	}

	// дальше идут служебные функции

	/**
	 * Сделать строку для запроса where из фильтра
	 * @param  array $_filter
	 * @return string
	 */
	protected function strWhere($_filter)
	{
		// ...
	}

	/**
	 * Сделать строку для сортировки
	 * @param  array $_sort
	 * @return string
	 */
	protected function strOrder($_sort)
	{
		// ...
	}

	/**
	 * Сделать строку с параметрами выборки
	 * @param  $_params
	 * @return string
	 */
	protected function strParams($_params)
	{
		// ...
	}
}

?>