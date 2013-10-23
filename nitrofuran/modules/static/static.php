<?php

/**
 * Класс, описывающие модель статичных страниц
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

class staticpage
{
	/**
	 * Конструктор
	 */
	public function __construct() { }

	/**
	 * Удалить страницу
	 * @param int $page_id
	 */
	public function delete($page_id)
	{
		$page_id = (int)$page_id;
		if(!$page_id)
		{
			return;
		}
		global $DB;
		$DB->TransactionStart();
		$DB->Query("delete from `".STATIC_META_TABLE."` where `page_id` = '".$page_id."'");
		$DB->Query("delete from `".STATIC_PAGES_TABLE."` where `id` = '".$page_id."'");
		$DB->TransactionCommit();
	}

	/**
	 * Получить данные об одной статичной странице
	 * @param  int  $id         идентификатор выбираемой страницы
	 * @param  bool $bUseTreeId инспользовать идентификатор как tree_id
	 * @return array
	 */
	public function page($id, $bUseTreeId = false)
	{
		global $DB;
		$result = array('page' => '', 'meta' => array());
		$id     = (int)$id;
		$res    = $DB->Query("select * from `".STATIC_PAGES_TABLE."` where `".($bUseTreeId ? 'tree_id' : 'id')."` = '".$id."'");
		$result['page'] = $DB->Fetch($res);
		$res    = $DB->Query("select `id`, `meta_key`, `content` from `".STATIC_META_TABLE."` where `page_id` = '".$result['page']['id']."'");
		while($ar = $DB->Fetch($res))
		{
			$result['meta'][$ar['id']] = array('key' => $ar['meta_key'], 'value' => $ar['content']);
		}

		return $result;
	}

	/**
	 * Сохранение содержимого страницы
	 * @param int    $id      идентификатор страницы
	 * @param string $content содержимое страницы
	 */
	public function save_page_content($id, $content)
	{
		global $DB;
		$id = (int)$id;
		$DB->Query("update `".STATIC_PAGES_TABLE."` set `content` = '".$DB->EscapeString($content)."' where `id` = '".$id."'");
	}

	/**
	 * Сохранение мета-данных страницы
	 * @param int   $id    идентификатор страницы
	 * @param array $_meta массив с данными вида array(id => array(key => meta_key, value => meta_value) [, ...])
	 */
	public function save_page_meta($id, $_meta)
	{
		global $DB;
		foreach($_meta as $meta_id => $meta_data)
		{
			$meta_id = (int)$meta_id;
			if(!$meta_id && strlen($meta_data['key']))
			{
				// если нет meta_id, то можно добавить новое свойство
				$DB->Query("insert into `".STATIC_META_TABLE."` (`page_id`, `meta_key`, `content`) values
					('".$id."', '".addslashes($meta_data['key'])."', '".addslashes($meta_data['value'])."')");
			}
			else
			{
				if(!strlen($meta_data['key']))
				{
					// если есть meta_id, но нет meta_data[key], то удалим свойство
					$DB->Query("delete from `".STATIC_META_TABLE."` where `id` = '".$meta_id."'");
				}
				else
				{
					// обновим свойство
					$DB->Query("update `".STATIC_META_TABLE."` set
						`meta_key` = '".addslashes($meta_data['key'])."',
						`content`  = '".addslashes($meta_data['value'])."'
						where `id` = '".$meta_id."'");
				}
			}
		}
	}
}

?>