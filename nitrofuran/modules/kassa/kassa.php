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
	 * Добавить операцию в кассу
	 * @param  array  $_fields       параметры добавления операции
	 * @param  string $error_code    возвращаемый код ошибки
	 * @param  string $error_message возвращаемый текст ошибки
	 * @return bool
	 */
	public function Add($_fields, &$error_code, &$error_message)
	{
		$error_code    = '';
		$error_message = '';

		// проверка суммы
		$_fields['amount'] = str_replace(',', '.', $_fields['amount']);
		$_fields['amount'] = round((float)$_fields['amount'], 2);
		if(!$_fields['amount'])
		{
			$error_code    = 'NO_AMOUNT';
			$error_message = 'Не указана сумма операции';
			return false;
		}

		// проверка типа операции
		$_fields['optype'] = (int)$_fields['optype'];
		if(!$_fields['optype'])
		{
			$error_code    = 'NO_TYPE';
			$error_message = 'Не указан тип операции';
			return false;
		}
		$_optypes = $this->GetOptypes();
		$bExists = false;
		foreach($_optypes['optypes'] as $v)
		{
			if($v['id'] == $_fields['optype'])
			{
				$bExists = true;
			}
		}
		if(!$bExists)
		{
			$error_code    = 'WRONG_TYPE';
			$error_message = 'Указан несуществующий тип операции';
			return false;
		}

		// проверка счёта
		$_fields['account'] = (int)$_fields['account'];
		$_accounts = $this->GetAccounts();
		if($_fields['account'])
		{
			$bExists = false;
			foreach($_accounts as $v)
			{
				if($v['id'] == $_fields['account'])
				{
					$bExists = true;
				}
			}
			if(!$bExists)
			{
				$error_code    = 'WRONG_ACCOUNT';
				$error_message = 'Указан несуществующий счёт операции';
				return false;
			}
		}
		else
		{
			foreach($_accounts as $v)
			{
				if($v['default'])
				{
					$_fields['account'] = $v['id'];
					break;
				}
			}
			if(!$_fields['account'])
			{
				$error_code    = 'NO_ACCOUNT';
				$error_message = 'Не указан счёт операции';
				return false;
			}
		}

		// проверка валюты
		$_fields['currency'] = (int)$_fields['currency'];
		$_currencies = $this->GetCurrencies();
		if($_fields['currency'])
		{
			$bExists = false;
			foreach($_currencies as $v)
			{
				if($v['id'] == $_fields['currency'])
				{
					$bExists = true;
				}
			}
			if(!$bExists)
			{
				$error_code    = 'WRONG_CURRENCY';
				$error_message = 'Указана несуществующая валюта операции';
				return false;
			}
		}
		else
		{
			foreach($_currencies as $v)
			{
				if($v['default'])
				{
					$_fields['currency'] = $v['id'];
					break;
				}
			}
			if(!$_fields['currency'])
			{
				$error_code    = 'NO_CURRENCY';
				$error_message = 'Не указана валюта операции';
				return false;
			}
		}

		// если добавление задним числом
		$_fields['backtime'] = strtotime(date('Y-m-d H:i:s', $_fields['backtime'] ? $_fields['backtime'] : time()));

		global $DB;
		$query_string = "insert into `".KASSA_OPERATION_TABLE."` (`currency_id`, `account_id`, `type_id`,
			`amount`, `time`, `comment`, `backtime`) values
			(
				'".$_fields['currency']."',
				'".$_fields['account']."',
				'".$_fields['optype']."',
				'".$_fields['amount']."',
				unix_timestamp(),
				'".$DB->EscapeString($_fields['comment'])."',
				'".$_fields['backtime']."'
			)";
		return $DB->Query($query_string);
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
	 * Получить список операций в соответствии с параметрами фильтрации.
	 * @param  array $_filter параметры фильтрации
	 * @return array
	 */
	public function GetOperations($_filter = array())
	{
		global $DB;
		$query_string = "select distinct op.*, c.name as currency, a.name as account, t.name as optype
			from `".KASSA_OPERATION_TABLE."` op
			join `".KASSA_CURRENCY_TABLE."` c on (c.`id` = op.`currency_id`"
				.($_filter['currency'] ? " and c.`id` = '".(int)$_filter['currency']."'" : '').")
			join `".KASSA_ACCOUNT_TABLE."` a on (a.`id` = op.`account_id`"
				.($_filter['account'] ? " and a.`id` = '".(int)$_filter['account']."'" : '').")
			join `".KASSA_OPERATION_TYPE_TABLE."` t on (t.`id` = op.`type_id`"
				.($_filter['optypegroup'] ? " and t.`group_id` = '".(int)$_filter['optypegroup']."'" : '')
				.($_filter['optype'] ? " and t.`id` = '".(int)$_filter['optype']."'" : '')
			.")
			where op.`backtime` >= '".($_filter['date_start'] ? strtotime($_filter['date_start']) : time() - 7 * 86400)."'
				and op.`backtime` <= '".($_filter['date_end']   ? strtotime($_filter['date_end'])   : time())."'
			order by op.`id` desc";
		return $DB->QueryFetched($query_string);
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