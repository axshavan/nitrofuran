<?php

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/config.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/crud.class.php');

/**
 * Описание класса для управления отдельными блогами
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
	 * @param  array $_fields список значений полей
	 * @return bool
	 */
	public static function Add($_fields)
	{
		$CRUD = new CRUD();
		if(!$_fields['name'] || !$_fields['user_id'])
		{
			return false;
		}
		if($_fields['tree_id'])
		{
			if(sizeof(CBlog::GetList(array('tree_id' => $_fields['tree_id']))))
			{
				return false;
			}
		}
		if
		(
			!$CRUD->create
			(
				BLOG_TABLE,
				array
				(
					'name'    => $_fields['name'],
					'user_id' => (int)$_fields['user_id'],
					'tree_id' => (int)$_fields['tree_id']
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
	 * @param  int  $id идентификатор удаляемого поста
	 * @return bool
	 */
	public static function Delete($id)
	{
		$CRUD = new CRUD();
		if(!$CRUD->delete(BLOG_TABLE, array('id' => $id)))
		{
			return false;
		}
		require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/blog_post.php');
		return CBlogPost::DeleteAllInBlog($id);
	}

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
		if(!$_fields['name'] || !$_fields['user_id'])
		{
			return false;
		}
		if($_fields['tree_id'])
		{
			if(sizeof(CBlog::GetList(array('tree_id' => $_fields['tree_id'], '!id' => $id))))
			{
				return false;
			}
		}
		$CRUD->update
		(
			BLOG_TABLE,
			array
			(
				'id' => (int)$id
			),
			array
			(
				'name'    => $_fields['name'],
				'user_id' => (int)$_fields['user_id'],
				'tree_id' => (int)$_fields['tree_id']
			)
		);
		return true;
	}

	/**
	 * Получить список постов
	 * @param  array $_filter параметры фильтрации списка
	 * @param  array $_sort   параметры сортировки списка
	 * @param  array $_params array(limit => limit, offset => offset)
	 * @return mixed
	 */
	public static function GetList($_filter = array(), $_sort = array(), $_params = array())
	{
		$CRUD = new CRUD();
		return $CRUD->read(BLOG_TABLE, $_filter, $_sort, $_params);
	}
}

?>