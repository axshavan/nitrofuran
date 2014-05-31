<?php

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/crud.class.php');

/**
 * Описание классов для управления постами в блоге.
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */
class CBlogPost
{
	/**
	 * Добавить пост
	 * @param  array $_fields список значений полей
	 * @return bool
	 */
	public static function Add($_fields)
	{
		$CRUD = new CRUD();
		if(!$_fields['title'] || !$_fields['text'])
		{
			return false;
		}
		if
		(
			!$CRUD->create
			(
				BLOG_POST_TABLE,
				array
				(
					'title'       => $_fields['title'],
					'blog_id'     => (int)$_fields['blog_id'],
					'text'        => $_fields['text'],
					'date_create' => strtotime($_fields['date_create'])
				)
			)
		)
		{
			return false;
		}
		return true;
	}

	/**
	 * Удалить пост
	 * @param int $id идентификатор удаляемого поста
	 */
	public static function Delete($id)
	{
		// ...
	}

	/**
	 * Удаление всех постов в блоге
	 * @param  int  $blog_id ид блога
	 * @return bool
	 */
	public static function DeleteAllInBlog($blog_id) { return true; }

	/**
	 * Редактиовать пост
	 * @param  int   $id      идентификатор редактируемого поста
	 * @param  array $_fields значения изменяемых полей
	 * @return bool
	 */
	public static function Edit($id, $_fields)
	{
		if(!$id)
		{
			return false;
		}
		$CRUD = new CRUD();
		if(!$_fields['title'] || !$_fields['text'])
		{
			return false;
		}
		$CRUD->update
		(
			BLOG_POST_TABLE,
			array
			(
				'id' => (int)$id
			),
			array
			(
				'title'       => $_fields['title'],
				'blog_id'     => (int)$_fields['blog_id'],
				'text'        => $_fields['text'],
				'date_create' => strtotime($_fields['date_create'])
			)
		);
		return true;
	}

	/**
	 * Получить количество постов
	 * @param  array $_filter массив с парметрами фильтрации
	 * @return int
	 */
	public static function GetCount($_filter = array())
	{
		$CRUD = new CRUD();
		$result = $CRUD->read(BLOG_POST_TABLE, $_filter, array(), array('get_count' => true));
		$result = array_values($result[0]);
		return $result[0];
	}

	/**
	 * Получить список постов
	 * @param  array $_filter параметры фильтрации списка
	 * @param  array $_sort   параметры сортировки списка
	 * @param  array $_params array(limit => limit, offset => offset)
	 * @return array
	 */
	public static function GetList($_filter = array(), $_sort = array(), $_params = array())
	{
		$CRUD = new CRUD();
		return $CRUD->read(BLOG_POST_TABLE, $_filter, $_sort, $_params);
	}
}

?>