<?php

/**
 * Описание классов для управления отдельными блогами
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */
class CBlog
{
	/**
	 * Добавить пост
	 * @param array $_fields список значений полей
	 */
	public function add($_fields)
	{
		// ...
	}

	/**
	 * Удалить пост
	 * @param int $id идентификатор удаляемого поста
	 */
	public function delete($id)
	{
		// ...
	}

	/**
	 * Редактиовать пост
	 * @param int   $id      идентификатор редактируемого поста
	 * @param array $_fields значения изменяемых полей
	 */
	public function edit($id, $_fields)
	{
		// ...
	}

	/**
	 * Получить список постов
	 * @param array $_filter параметры фильтрации списка
	 * @param array $_sort   параметры сортировки списка
	 * @param array $_params array(limit => limit, offset => offset)
	 */
	public function getList($_filter, $_sort, $_params)
	{
		// ...
	}
}

?>