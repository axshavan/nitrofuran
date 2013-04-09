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
	 * Добавить непрочитанный элемент подписки.
	 * @param  array $item
	 * @param  int   $subscription_id идентификатор подписки
	 * @return int   идентификатор добавленного элемента
	 */
	public function addItem($item, $subscription_id)
	{
		$res = $this->crud->read
		(
			READER_SUBSCRIPTION_ITEM_TABLE,
			array
			(
				'subscription_id' => $subscription_id,
				'href'            => $item['href'],
			)
		);
		if(!$res[0]['id'])
		{
			$this->crud->create
			(
				READER_SUBSCRIPTION_ITEM_TABLE,
				array
				(
					'name'            => $item['title'],
					'href'            => $item['href'],
					'subscription_id' => $subscription_id,
					'read_flag'       => 0,
					'date'            => (int)$item['date'] ? (int)$item['date'] : time(),
				)
			);
			return $this->crud->id();
		}
		else
		{
			return $res[0]['id'];
		}
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

	/**
	 * Получить список элементов подписки
	 * @param  array $subscription данные о подписке
	 * @return array
	 */
	public function getItems($subscription)
	{
		$curl = curl_init($subscription['href']);
		ob_start();
		curl_exec($curl);
		$xml = simplexml_load_string(ob_get_clean());

		if($xml)
		{
			// может быть, это что-то похожее на RSS 2.0
			if((string)$xml->attributes()->version == '2.0' && $xml->channel)
			{
				$data = $this->parseRSS20($xml);
			}
		}

		$mostEarlierDate = time();
		foreach($data['items'] as &$item)
		{
			if(!$item['date'])
			{
				$item['date'] = time();
			}
			elseif($item['date'] < $mostEarlierDate)
			{
				$mostEarlierDate = $item['date'];
			}
			$item['id'] = $this->addItem($item, $subscription['id']);
		}
		$res = $this->crud->read
		(
			READER_SUBSCRIPTION_ITEM_TABLE,
			array
			(
				'subscription_id' => $subscription['id'],
				'read_flag'       => 0,
				'<date'           => $mostEarlierDate
			)
		);
		foreach($res as $res_row)
		{
			if($res_row['date'] < $mostEarlierDate)
			{
				$mostEarlierDate = $res_row['date'];
			}
			foreach($data['items'] as &$item)
			{
				if($item['href'] == $res_row['href'])
				{
					continue 2;
				}
			}
			$res_row['title'] = $res_row['name'];
			$data['items'][]  = $res_row;
		}
		$res = $this->crud->read
		(
			READER_SUBSCRIPTION_ITEM_TABLE,
			array
			(
				'subscription_id' => $subscription['id'],
				'read_flag'       => 1,
				'>=date'           => $mostEarlierDate
			)
		);
		foreach($res as $res_row)
		{
			foreach($data['items'] as $k => &$item)
			{
				if($item['href'] == $res_row['href'])
				{
					unset($data['items'][$k]);
				}
			}
		}
		usort($data['items'], create_function('$a, $b', 'return $a["date"] < $b["date"] ? 1 : ($a["date"] > $b["date"] ? -1 : 0);'));
		return $data;
	}

	/**
	 * Получить данные об одной подписки
	 * @param  int $id идентификатор подписки
	 * @return array
	 */
	public function getSubscription($id)
	{
		$result = $this->crud->read(READER_SUBSCRIPTION_TABLE, array('id' => (int)$id));
		$result = $result[0];
		$result['items'] = $this->getItems($result);
		return $result;
	}

	/**
	 * Получить данные об одной группе подписок
	 * @param  int $id идентификатор группы подписок
	 * @return array
	 */
	public function getSubscriptionGroup($id)
	{
		$result = $this->crud->read(READER_SUBSCRIPTION_GROUP_TABLE, array('id' => (int)$id));
		return $result[0];
	}

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

	/**
	 * Пометить элемент прочитанным
	 * @param int $id
	 */
	public function readItem($id)
	{
		$this->crud->update(READER_SUBSCRIPTION_ITEM_TABLE, array('id' => $id), array('read_flag' => 1));
	}

	public function refreshAll() {}
	public function refreshSubscription() {}
	public function unreadItem() {}

	/**
	 * Обновить группу подписок
	 * @param  $id       идентификатор группы
	 * @param  $name     новое название группы
	 * @param  $group_id новая родительяская группа для этой группы
	 * @return bool
	 */
	public function updateGroup($id, $name, $group_id)
	{
		return $this->crud->update
		(
			READER_SUBSCRIPTION_GROUP_TABLE,
			array('id' => (int)$id),
			array
			(
				'name'     => $name,
				'group_id' => (int)$group_id
			)
		);
	}

	/**
	 * Обновить подписку
	 * @param  int    $id       идентификатор обновляемой подписки
	 * @param  string $name     новое название для подписки
	 * @param  int    $group_id новая группа для подписки
	 * @return bool
	 */
	public function updateSubscription($id, $name, $group_id)
	{
		return $this->crud->update
		(
			READER_SUBSCRIPTION_TABLE,
			array('id' => (int)$id),
			array
			(
				'name'     => $name,
				'group_id' => (int)$group_id
			)
		);
	}

	public function updateItem() {}

	// PROTECTED AREA

	/**
	 * Достать элементы из  RSS 2.0
	 * @param  SimpleXMLElement $xml
	 * @return array
	 */
	protected function parseRSS20(&$xml)
	{
		$_result = array
		(
			'meta' => array
			(
				'title'         => (string)$xml->channel->title,
				'link'          => (string)$xml->channel->link,
				'description'   => (string)$xml->channel->description,
				'lastBuildDate' => (string)$xml->channel->lastBuildDate,
				'image'         => array
				(
					'url'   => (string)$xml->channel->image->url,
					'link'  => (string)$xml->channel->image->link,
					'title' => (string)$xml->channel->image->title
				)
			),
			'items' => array()
		);
		foreach($xml->channel->item as $item)
		{
			$_result['items'][] = array
			(
				'title'       => (string)$item->title,
                'href'        => (string)$item->link,
                'description' => (string)$item->description,
                'date'        => (string)$item->pubDateUT
			);
		}
		return $_result;
	}
}

?>