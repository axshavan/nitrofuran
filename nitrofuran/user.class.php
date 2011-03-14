<?php

/*
 * Набор функций для работы с пользователями.
 */

class CUser
{
	/*
		Создание пользователя.
		@param  string $login     логин
		@param  string $password  пароль
		@param  string $email     емейл или что-то в этом роде
		@param  string $full_name полное имя пользователя (как к нему обращаться на сайте)
		@param  string $error     здесь возвращается текст ошибки
		@return mixed  номер созданного пользователя или false в случае ошибки
	*/
	public static function Add($login, $password, $email, $full_name, &$error)
	{
		global $DB;
		$error = '';
		$email = substr($DB->EscapeString($email), 0, 255);
		$login = substr($DB->EscapeString($login), 0, 32);
		if(preg_match('/[^a-z0-9]/', $login))
		{
			$error = 'BAD LOGIN';
			return false;
		}
		$fname = substr($DB->EscapeString($full_name), 0, 255);
		$res   = $DB->Query("select 1 from `".USERS_TABLE."` where `email` = '".$email."' or `login` = '".$login."'");
		$_row  = $DB->Fetch($res);
		if($_row[1])
		{
			$error = 'USER EXISTS';
			return false;
		}
		$DB->TransactionStart();
		if(!$DB->Query("insert into `".USERS_TABLE."`
			(`login`, `email`, `password`, `full_name`, `regdate`, `authkey`) values
			('".$login."', '".$email."', md5(concat('".md5($password)."', ' qjBDY65$#/')), '".$full_name."', unix_timestamp(), '')"))
		{
			echo $DB->Error();
			$DB->TransactionRollback();
			$error = 'DB ERROR';
			return false;
		}
		$DB->TransactionCommit();
		return $DB->InsertedId();
	}
	
	/*
		Попытка залогиниться.
		@param  string $login    логин
		@param  string $password пароль
		@param  bool   $remember длинная сессия
		@param  bool   $bind2ip  привязать к ip
		@param  string $error    возвращается код ошибки
		@return bool
	*/
	public static function Login($login, $password, $remember, $bind2ip, &$error)
	{
		global $AUTH;
		return $AUTH->Login($login, $password, $remember, $bind2ip, &$error);
	}
	
	/*
		Разлогиниться.
		@return bool
	*/
	public static function Logout()
	{
		global $AUTH;
		return $AUTH->Logout();
	}
}

?>