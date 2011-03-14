<?php

// 000014
// создание пользователя admin

global $DB;

if(isset($_POST['admin_password']))
{
	global $AUTH;
	CUser::Add('admin', $_POST['admin_password'], $_POST['admin_email'], '', &$error);
	$AUTH->Authorize(1);
	return true;
}
else
{
	$res  = $DB->Query("select 1 from `".USERS_TABLE."` where `login` = 'admin'");
	$_row = $DB->Fetch($res);
	if(!$_row[1])
	{
		header("Content-Type: text/html; charset=utf-8");
		?>
		<form action="/admin/?module=update&page=1&proceed" method="post" id="submit_form">
			Введите пароль и адрес электронной почты (адрес не обязательно) для пользователя admin<br>
			<input type="text" name="admin_password" id="admin_password" value="<?= MYSQL_PASSWORD ?>">
			<label for="admin_password">Пароль (имейте в виду, вводится без экранирования символов)</label><br>
			<input type="text" name="admin_email" id="admin_email" value="">
			<label for="admin_email">Адрес электронной почты</label><br>
			<input type="button" value="Ок" onclick="document.getElementById('submit_form').submit();">
		</form>
		<?
		die();
	}
	else
	{
		return true;
	}
}

?>