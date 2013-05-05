<?php

/**
 * Аяксовый маршрутизатор модуля reader
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

switch($_POST['ajax'])
{
	// добавление группы
	case 'addGroup':
	{
		if($reader->addGroup($_POST['group'], $_POST['parent_id'], $error))
		{
			echo 'ok';
		}
		else
		{
			echo 'Не удалось добавить группу: ';
			switch($error)
			{
				case 'EMPTY_NAME': echo 'пустое название группы'; break;
				case 'DB_ERROR':   echo 'ошибка базы данных'; break;
				default:           echo 'непонятно почему'; break;
			}
		}
		break;
	}

	// добавление подписки
	case 'addSubscription':
	{
		if($reader->addSubscription($_POST['href'], $_POST['group_id'], $error))
		{
			echo 'ok';
		}
		else
		{
			echo 'Не удалось добавить подписку: ';
			switch($error)
			{
				case 'EMPTY_NAME': echo 'пустая ссылка'; break;
				case 'DB_ERROR':   echo 'ошибка базы данных'; break;
				default:           echo 'непонятно почему'; break;
			}
		}
		break;
	}

	// удаление подписки
	case 'delSubscription':
	{
		if($reader->deleteSubscription($_POST['id'], $error))
		{
			echo 'ok';
		}
		else
		{
			echo 'Не удалось удалить подписку: ';
			switch($error)
			{
				case 'NO_ID': echo 'нет идентификатора подписки'; break;
				default:      echo 'непонятно почему'; break;
			}
		}
		break;
	}

	// получить данные об одной подписке
	case 'getSubsription':
	{
		$result = $reader->getSubscription($_POST['id']);
		echo json_encode($result);
		break;
	}

	// получить список элементов подписки
	case 'getSubscriptionItems':
	{
		$result = $reader->getSubscription($_POST['id']);
		$result = $reader->getItems($result);
		$tplengine->assign('items', $result);
		$tplengine->template('items.tpl');
		break;
	}

	// получить данные об одной группе подписок
	case 'getSubsriptionGroup':
	{
		echo json_encode($reader->getSubscriptionGroup($_POST['id']));
		break;
	}

	// загрузка списка подписок
	case 'loadSubscriptions':
	{
		$subscriptions = $reader->getSubscriptions();
		$tplengine->assign('tree', $subscriptions);
		$data = array
		(
			'subscr'          => $tplengine->template('index_left.tpl', true),
			'editsform_group' => $tplengine->template('group_plain.tpl', true)
		);
		echo json_encode($data);
		break;
	}

	// пометить элемент прочитанным
	case 'markAsRead':
	{
		$reader->readItem($_POST['item_id']);
		echo 'ok';
		break;
	}

	// сохранение одной подписки
	case 'saveSubscription':
	{
		if(!$reader->updateSubscription($_POST['id'], $_POST['name'], $_POST['group_id']))
		{
			echo 'Не удалось обновить подписку';
		}
		else
		{
			echo 'ok';
		}
		break;
	}

	// сохранение группы подписок
	case 'saveSubscriptionGroup':
	{
		if(!$reader->updateGroup($_POST['id'], $_POST['name'], $_POST['group_id']))
		{
			echo 'Не удалось обновить папку';
		}
		else
		{
			echo 'ok';
		}
		break;
	}
}
die();

?>