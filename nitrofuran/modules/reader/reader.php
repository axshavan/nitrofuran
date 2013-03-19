<?php

/**
 * Описание классов моделей для модуля reader
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

require_once(DOCUMENT_ROOT.'/nitrofuran/crud.class.php');

class CReader
{
	protected $crud; // CRUD модель

	// конструктор
	public function __construct()
	{
		$this->crud = new CRUD();
	}

	/**
	 * Добавление группы подписок
	 * @param  string $group_name      имя новой группы
	 * @param  int    $parent_group_id id группы-родителя
	 * @param  string &$error          тут возвращается код ошибки
	 * @return bool
	 */
	public function addGroup($group_name, $parent_group_id, &$error)
	{
		$error = '';
		if(!$group_name || !sizeof($group_name))
		{
			$error = 'EMPTY_NAME';
			return false;
		}
		if(!$this->crud->create(READER_SUBSCRIPTION_GROUP_TABLE, array('name' => $group_name, 'group_id' => (int)$parent_group_id)))
		{
			$error = 'DB_ERROR';
			return false;
		}
		return true;
	}

	/**
	 * Добавить подписку
	 * @param  string $href     собственно ссылка на фид
	 * @param  int    $group_id id группы, куда добавляется подписка
	 * @param  string &$error   тут возвращается ошибка
	 * @return bool
	 */
	public function addSubscription($href, $group_id, &$error)
	{
		$error = '';
		if(!$href || !sizeof($href))
		{
			$error = 'EMPTY_HREF';
			return false;
		}
		global $AUTH;
		if
		(
			!$this->crud->create
			(
				READER_SUBSCRIPTION_TABLE,
				array
				(
					'name' => $href,
					'href' => $href,
					'group_id' => (int)$group_id,
					'user_id'  => $AUTH->sess_data['user_id']
				)
			)
		)
		{
			$error = 'DB_ERROR';
			return false;
		}
		return true;
	}

	public function deleteGroup() {}
	public function deleteSubscription() {}
	public function getItem() {}
	public function getItems() {}

	/**
	 * Получить список папок и подписок
	 * @return array
	 */
	public function getSubscriptions()
	{
		require_once(DOCUMENT_ROOT.'/nitrofuran/graph.class.php');
		global $AUTH;

		// надо собрать массив с ключами, соответствующими id групп
		$res_g = array();
		$tmp   = $this->crud->read(READER_SUBSCRIPTION_GROUP_TABLE);
		foreach($tmp as $v)
		{
			$res_g[$v['id']] = $v;
		}
		unset($tmp);

		// выборка подписок и раскладывание их по папкам
		$res_s = $this->crud->read(READER_SUBSCRIPTION_TABLE, array('user_id' => $AUTH->sess_data['user_id']));
		foreach($res_s as $v)
		{
			$res_g[$v['group_id']]['subscriptions'][$v['id']] = $v;
		}
		unset($res_s);

		// а теперь завернём всё в граф
		$graph = new CGraph();
		$graph->CreateFromArray($res_g, 'group_id');
		$result = $graph->GetAsArray(true);

		// вынос подписок без группы в корень
		foreach($result['children'][0]['data']['subscriptions'] as $v)
		{
			$result['data']['subscriptions'][] = $v;
		}
		unset($result['children'][0]);
		return $result;
	}

	public function readItem() {}
	public function refreshAll() {}
	public function refreshSubscription() {}
	public function unreadItem() {}
	public function updateGroup() {}
	public function updateSubscription() {}
	public function updateItem() {}
}

?>