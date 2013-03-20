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

	// получить данные об одной подписке
	case 'getSubsription':
	{
		// ...
		break;
	}

	// загрузка списка подписок
	case 'loadSubscriptions':
	{
		$tplengine->assign('tree', $reader->getSubscriptions());
		$tplengine->template('index_left.tpl');
		break;
	}
}
die();

?>