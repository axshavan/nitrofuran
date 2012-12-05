<?php

$tplengine = new CTemplateEngine('auth');
if($_POST)
{
	if(!CUser::Login($_POST['login'], $_POST['password'], $_POST['remember'], $_POST['bind2ip'], $error))
	{
		$tplengine->assign('login',    h($_POST['login']));
		$tplengine->assign('password', h($_POST['password']));
		$tplengine->assign('remember', $_POST['remember'] ? true : false);
		$tplengine->assign('bind2ip',  $_POST['bind2ip']  ? true : false);
		switch($error)
		{
			case 'BAD LOGIN':
			{
				$error = 'Логин может содержать только строчные английские буквы и цифры';
				break;
			}
			case 'WRONG PASSWORD':
			default:
			{
				$error = 'Неправильный пароль';
				break;
			}
		}
		$tplengine->assign('error', $error);
	}
	else
	{
		redirect('..');
		die();
	}
}
global $AUTH;
if($AUTH->user_data['id'])
{
	redirect('..');
	die();
}
$tplengine->template('login.tpl');

?>