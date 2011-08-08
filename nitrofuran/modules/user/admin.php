<?php

/*
	Страница администрирования пользователей.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

global $DB;

switch($_GET['page'])
{
	case 1:
	{
		if($_POST)
		{
			if(!CUser::Update(1, array('login' => $_POST['user_1_login'], 'password' => $_POST['user_1_password']), $error))
			{
				$error_text = 'Данные пользователя не обновлены: ';
				switch($error)
				{
					case 'NO_ID':    $error_text .= ' не указан пользователь'; break;
					case 'DB_ERROR': $error_text .= ' ошибка базы данных'; break;
					default:         $error_text .= ' неизвестная какая-то ошибка'; break;
				}
				$tplengine->assign('error_text', $error_text);
			}
			else
			{
				$tplengine->assign('success_text', 'Данные пользователя успешно обновлены');
			}
		}
		$res    = $DB->Query("select * from `".USERS_TABLE."`");
		$_users = array();
		while($_row = $DB->Fetch($res))
		{
			$_users[$_row['id']] = $_row;
		}
		$tplengine->assign('_users', $_users);
		$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/user/templates/userlist.tpl');
		break;
	}
}

?>