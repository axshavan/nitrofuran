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
			$_fields = array
			(
				'login'     => $_POST['login'],
				'email'     => $_POST['email'],
				'full_name' => $_POST['full_name']
			);
			if(strlen($_POST['newpassword']))
			{
				$_fields['password'] = $_POST['newpassword'];
			}
			if(!CUser::Update((int)$_POST['id'], $_fields, $error))
			{
				$error_text = 'Данные пользователя не обновлены: ';
				switch($error)
				{
					case 'NO_ID':              $error_text .= ' не указан пользователь'; break;
					case 'DB_ERROR':           $error_text .= ' ошибка базы данных'; break;
					case 'LOGIN_EMAIL_EXISTS': $error_text .= ' существует другой пользователь с таким логином или емейлом'; break;
					case 'EMPTY_LOGIN':        $error_text .= ' пустой логин'; break;
					default:                   $error_text .= ' неизвестная какая-то ошибка'; break;
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
	case 2:
	{
		if($_POST)
		{
			$error      = '';
			$error_text = '';
			if(!CUser::Add($_POST['login'], $_POST['password'], $_POST['email'], $_POST['full_name'], $error))
			{
				$error_text = 'Пользователь не создан: ';
				switch($error)
				{
					case 'BAD LOGIN':   $error_text .= ' плохой логин'; break;
					case 'USER EXISTS': $error_text .= ' такой пользователь уже есть'; break;
					case 'DB ERROR':    $error_text .= ' неизвестная ошибка'; break;
				}
				$tplengine->assign('error_text', $error_text);
			}
			else
			{
				$tplengine->assign('success_text', 'Пользователь создан');
			}
		}
		$tplengine->assign('inner_template_name', DOCUMENT_ROOT.'/nitrofuran/modules/user/templates/usernew.tpl');
	}
}

?>