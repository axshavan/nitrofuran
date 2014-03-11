<?php

/**
 * Набор функций для работы с пользователями.
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

class CUser
{
	/**
     * Создание пользователя.
     * @param  string $login     логин
     * @param  string $password  пароль
     * @param  string $email     емейл или что-то в этом роде
     * @param  string $full_name полное имя пользователя (как к нему обращаться на сайте)
     * @param  string $error     здесь возвращается текст ошибки
     * @return mixed  номер созданного пользователя или false в случае ошибки
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
		$result = $DB->InsertedId();
		$DB->TransactionCommit();
		return $result;
	}
	
	/**
     * Попытка залогиниться.
     * @param  string $login    логин
     * @param  string $password пароль
     * @param  bool   $remember длинная сессия
     * @param  bool   $bind2ip  привязать к ip
     * @param  string $error    возвращается код ошибки
     * @return bool
	 */
	public static function Login($login, $password, $remember, $bind2ip, &$error)
	{
		global $AUTH;
		return $AUTH->Login($login, $password, $remember, $bind2ip, $error);
	}
	
	/**
     * Разлогиниться.
     * @return bool
	 */
	public static function Logout()
	{
		global $AUTH;
		return $AUTH->Logout();
	}
	
	/**
     * Изменить пользователя.
     * @param  int    $id      id пользователя
     * @param  array  $_fields массив со значениями полей
     * @param  string &$error  возвращается код ошибки
     * @return bool
	 */
	public static function Update($id, $_fields, &$error)
	{
		global $DB;
		
		$error = '';
		$id    = (int)$id;
		if(!$id)
		{
			$error = 'NO_ID';
			return false;
		}
		if(!sizeof($_fields))
		{
			return true;
		}
		// логин должен иметь хотя бы какую-нибудь длину
		if(isset($_fields['login']) && !strlen($_fields['login']))
		{
			$error = 'EMPTY_LOGIN';
			return false;
		}
		// проверка на существование такого логина или такого емейла
		if(isset($_fields['login']) || isset($_fields['email']))
		{
			$sql_str = "select `id` from `".USERS_TABLE."` where 1 = 1 and ("
				.(isset($_fields['login']) ? "`login` = '".$DB->EscapeString($_fields['login'])."' or " : '')
				.(isset($_fields['email']) ? "`email` = '".$DB->EscapeString($_fields['email'])."' or " : '')
				." 1 = 1) and `id` != '".$id."'";
			$res = $DB->Query($sql_str);
			$res = $DB->Fetch($res);
			if(sizeof($res) && $res)
			{
				$error = 'LOGIN_EMAIL_EXISTS';
				return false;
			}
		}
		$sql_str = "update `".USERS_TABLE."` set";
		if(isset($_fields['login']))
		{
			$_fields['login'] = trim($_fields['login']);
			if(strlen($_fields['login']))
			{
				$sql_str .= " `login` = '".$DB->EscapeString($_fields['login'])."',";
			}
		}
		if(isset($_fields['email']))
		{
			$_fields['email'] = trim($_fields['email']);
			if(strlen($_fields['email']))
			{
				$sql_str .= " `email` = '".$DB->EscapeString($_fields['email'])."',";
			}
		}
		if(isset($_fields['full_name']))
		{
			$_fields['full_name'] = trim($_fields['full_name']);
			if(strlen($_fields['full_name']))
			{
				$sql_str .= " `full_name` = '".$DB->EscapeString($_fields['full_name'])."',";
			}
		}
		if(isset($_fields['password']) && strlen($_fields['password']))
		{
			$_fields['password'] = md5(md5($_fields['password']).' qjBDY65$#/');
			$sql_str .= " `password` = '".$_fields['password']."',";
		}
		$sql_str = substr($sql_str, 0, strlen($sql_str) - 1);
		$sql_str .= " where `id` = '".$id."'";
		$res = $DB->Query($sql_str);
		if(!$res)
		{
			$error = 'DB_ERROR';
			return false;
		}
		return true;
	}
}

?>