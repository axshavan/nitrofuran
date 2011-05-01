<?php

/*
	Класс работы с базой данных.
	Все обращения к базе данных настоятельно рекомендуется делать через объекты
	данного класса.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

class CDatabase
{
	protected $cid = false;
	
	/*
		Количество записей, на которые повлиял предыдущий запрос.
		@return int
	*/
	public function AffectedRows()
	{
		return mysql_affected_rows($this->cid);
	}
	
	/*
		Соединение с базой данных.
		@param string $host хост, куда цепляться
		@param string $user пользователь
		@param string $password пароль
		@param string $database имя базы данных
	*/
	public function Connect($host, $user, $password, $database)
	{
		$this->cid = mysql_connect($host, $user, $password, true);
		if(!$this->cid)
		{
			echo mysql_error();
			die();
		}
		if(!mysql_select_db($database, $this->cid))
		{
			echo mysql_error($this->cid);
			die();
		}
		$this->Query("set names utf8");
	}
	
	/*
		Отцепиться от базы данных.
	*/
	public function Disconnect()
	{
		mysql_close($this->cid);
		$this->cid = false;
	}
	
	/*
		Заэскейпить строку, чтоб её можно было безопасно вставлять в запрос.
		@param  string $str
		@return string
	*/
	public function EscapeString($str)
	{
		return str_replace("'", '"', str_replace('`', '"', $str));
	}
	
	/*
		Показать последнюю ошибку из БД.
		@return string
	*/
	public function Error()
	{
		return mysql_errno($this->cid).' '.mysql_error($this->cid);
	}
	
	/*
		Взять строку из результата выборки.
		@param resourse $result
		@return array
	*/
	public function Fetch($result)
	{
		return mysql_fetch_assoc($result);
	}
	
	/*
		Осуществить запрос к БД и получить сразу отфетченный результат.
		@param  string $str запрос
		@return array или false
	*/
	public function QueryFetched($str)
	{
		$res     = $this->Query($str);
		$_result = array();
		while($_row = $this->Fetch($res))
		{
			$_result[] = $_row;
		}
		return $_result;
	}
	
	/*
		Последнее значение автоинкрементного поля.
		@return int mysql_insert_id
	*/
	public function InsertedId()
	{
		return mysql_insert_id($this->cid);
	}
	
	/*
		Отправить запрос к базе данных.
		@param  string $str текст запроса
		@return resourse mysql result
	*/
	public function Query($str)
	{
		return mysql_query($str);
	}
	
	/*
		Завершить транзакцию.
	*/
	public function TransactionCommit()
	{
		$this->Query("commit");
	}
	
	/*
		Отменить транзакцию.
	*/
	public function TransactionRollback()
	{
		$this->Query("rollback");
	}
	
	/*
		Открыть транзакцию.
	*/
	public function TransactionStart()
	{
		$this->Query("begin");
	}	
}

?>