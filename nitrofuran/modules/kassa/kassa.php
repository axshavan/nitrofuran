<?php

/*
	Класс работы с сущностями кассы (для API)
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

class CKassa
{
	protected $authorized;

	/**
	 * Это конструктор, спасибо
	 */
	public function __construct()
	{
		$this->authorized = false;
	}

	/**
	 * Проверка права доступа к кассе
	 * @param  $login    логин
	 * @param  $password пароль
	 * @return bool
	 */
	public function CheckAccess($login, $password)
	{
		global $DB;
		$res = $DB->QueryFetched("select `id` from `".USERS_TABLE."` where
			`login` = '".$DB->EscapeString($login)."'
			 and `password` = md5(concat('".md5($password)."', ' qjBDY65$#/'))");
		if(!$res[0]['id'])
		{
			return false;
		}
		$user_id = $res[0]['id'];
		$res = $DB->QueryFetched("select * from `".TREE_TABLE."` where
			`name` = 'kassa' and `module` = 'kassa' and `action` = ''");
		return CModule::CheckAccessRight($res[0], array('id' => $user_id));
	}

	/**
	 * Получить список счетов в кассе
	 * @return array
	 */
	public function GetAccounts()
	{
		global $DB;
		return $DB->QueryFetched("select * from `".KASSA_ACCOUNT_TABLE."`");
	}

	/**
	 * Получить список валют в кассе
	 * @return array
	 */
	public function GetCurrencies()
	{
		global $DB;
		return $DB->QueryFetched("select * from `".KASSA_CURRENCY_TABLE."`");
	}

	/**
	 * Получить список типов операций и групп типов операций
	 * @return array
	 */
	public function GetOptypes()
	{
		global $DB;
		return array
		(
			'optypegroups' => $DB->QueryFetched("select * from `".KASSA_OPERATION_TYPE_GROUP_TABLE."`"),
			'optypes'      => $DB->QueryFetched("select * from `".KASSA_OPERATION_TYPE_TABLE."`")
		);
	}
}

?>