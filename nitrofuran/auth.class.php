<?php

/*
 * Набор функций для повседневного ведения сессий.
 */

class CAuth
{
	public $sess_data = array();
	public $user_data = array();
	
	/*
		Использовать только для внутренних нужд.
		@param int  $user_id  пользователь
		@param bool $remember запомнить сессию
		@param bool $bind2ip  привязать сессию к ip-адресу
	*/
	public function Authorize($user_id, $remember = true, $bind2ip = true)
	{
		global $DB;
		$user_id         = (int)$user_id;
		$res             = $DB->Query("select * from `".USERS_TABLE."` where `id` = '".$user_id."'");
		$this->user_data = $DB->Fetch($res);
		$ip              = $_SERVER['REMOTE_ADDR'];
		if(isset($_SERVER['X_HTTP_FORWARDED_FOR']))
		{
			$ip = $_SERVER['X_HTTP_FORWARDED_FOR'];
		}
		elseif(isset($_SERVER['X_HTTP_REAL_IP']))
		{
			$ip = $_SERVER['X_HTTP_REAL_IP'];
		}
		$DB->Query("insert into `".SESSIONS_TABLE."` (`md5id`, `user_id`, `last_action`, `ip`, `remember`, `bind2ip`)
			values ('', '".$user_id."', UNIX_TIMESTAMP(), inet_aton('".$ip."'), '".($remember ? 1 : 0)."', '".($bind2ip ? 1 : 0)."')");
		$sess_id = $DB->InsertedId();
		$md5id   = md5($sess_id);
		$DB->Query("update `".SESSIONS_TABLE."` set `md5id` = '".$md5id."' where `id` = '".$sess_id."'");
		$this->sess_data = array(
			'id'          => $sess_id,
			'md5id'       => $md5id,
			'user_id'     => $user_id,
			'last_action' => time(),
			'remember'    => $remember ? true : false,
			'bind2ip'     => $bind2ip  ? true : false
		);
		$hash2 = md5($this->sess_data['user_id'].$this->sess_data['md5id'].' qjBDY65$#/');
		setcookie(SESSION_COOKIE_NAME, $this->EncodeCookie($md5id, $hash2), ($remember ? SESSION_LIFETIME + time() : 0), '/');
		return true;
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
	public function Login($login, $password, $remember, $bind2ip, &$error)
	{
		global $DB;
		$login = substr($login, 0, 32);
		if(preg_match('/[^a-z0-9]/', $login))
		{
			$error = 'BAD LOGIN';
			return false;
		}
		$res  = $DB->Query("select `id` from `".USERS_TABLE."` where `login` = '".$login."' and `password` = md5(concat('".md5($password)."', ' qjBDY65$#/'))");
		$_row = $DB->Fetch($res);
		if(!$_row['id'])
		{
			$error = 'WRONG PASSWORD';
			return false;
		}
		return $this->Authorize($_row['id'], $remember, $bind2ip);
	}
	
	/*
		Разлогиниться.
		@return bool
	*/
	public function Logout()
	{
		$this->GuestSession();
		return true;
	}
	
	/*
		Стартануть сессию пользователя.
		@return bool залогинен ли пользователь
	*/
	public function StartSession()
	{
		global $DB;
		// кукис прилетел?
		if($_COOKIE[SESSION_COOKIE_NAME])
		{
			if(strlen($_COOKIE[SESSION_COOKIE_NAME]) != 64)
			{
				// это явно не два мд5-хэша
				return $this->GuestSession();
			}
			list($md5id, $hash2) = $this->DecodeCookie($_COOKIE[SESSION_COOKIE_NAME]);
			// заодно грохнем старые сессии
			$this->KillOldSessions();
			$res = $DB->Query("select `id`, `md5id`, `user_id`, `last_action`, `remember`, `bind2ip`, inet_ntoa(`ip`) as ip4 from `".SESSIONS_TABLE."` where `md5id` = '".$md5id."'");
			$this->sess_data = $DB->Fetch($res);
			if(!$this->sess_data)
			{
				// нет такой сессии
				return $this->GuestSession();
			}
			$this->sess_data['ip'] = $this->sess_data['ip4'];
			unset($this->sess_data['ip4']);
			// проверка привязку к айпишнику
			if($this->sess_data['bind2ip'])
			{
				if(
					(isset($_SERVER['X_HTTP_REAL_IP']) && $_SERVER['X_HTTP_REAL_IP'] != $this->sess_data['ip'])
					|| (isset($_SERVER['X_HTTP_FORWARDED_FOR']) && $_SERVER['X_HTTP_FORWARDED_FOR'] != $this->sess_data['ip'])
					|| ($_SERVER['REMOTE_ADDR'] != $this->sess_data['ip'])
				)
				{
					return $this->CreateGuestSession();
				}
			}
			// проверка второго хэша
			$hash1 = md5($this->sess_data['user_id'].$this->sess_data['md5id'].' qjBDY65$#/');
			if($hash1 != $hash2)
			{
				return $this->GuestSession();
			}
			// получим данные пользователя
			$res = $DB->Query("select * from `".USERS_TABLE."` where `id` = '".$this->sess_data['user_id']."'");
			$this->user_data = $DB->Fetch($res);
			// всё хорошо
			return $this->ProlongSession();
		}
		else
		{
			return $this->GuestSession();
		}
	}
	
	/*
		Из мешанины символов достать два хэша.
		@param  string $str то, что надо разобрать
		@return array
	*/
	protected function DecodeCookie($str)
	{
		$str1 = $str2 = '';
		for($i = 0; $i < 32; $i++)
		{
			$str1 .= $str[$i * 2 + 1];
			$str2 .= $str[$i * 2];
		}
		return array($str1, $str2);
	}
	
	/*
		Перемешать символы двух хэшей.
		@param  string $str1 первый хэш
		@param  string $str1 второй хэш
		@return string
	*/
	protected function EncodeCookie($str1, $str2)
	{
		$result = '';
		for($i = 0; $i < 32; $i++)
		{
			$result .= $str2[$i].$str1[$i];
		}
		return $result;
	}
	
	/*
		Начать сессию незарегистрированного пользователя или обнулить текущую сессию.
		@return false
	*/
	protected function GuestSession()
	{
		$this->user_data = array();
		$this->sess_data = array();
		setcookie(SESSION_COOKIE_NAME, '', 0, '/');
		return false;
	}
	
	/*
		Удалить истекшие сессии.
	*/
	protected function KillOldSessions()
	{
		global $DB;
		return $DB->Query("delete from `".SESSIONS_TABLE."` where `last_action` < unix_timestamp() - ".SESSION_LIFETIME);
	}
	
	/*
		Продлить текущую сессию.
	*/
	protected function ProlongSession()
	{
		global $DB;
		$DB->Query("update `".SESSIONS_TABLE."` set `last_action` = unix_timestamp() where `id` = '".$this->sess_data['id']."'");
		setcookie(SESSION_COOKIE_NAME, $_COOKIE[SESSION_COOKIE_NAME], $this->sess_data['remember'] ? SESSION_LIFETIME + time() : 0, '/');
		return true;
	}
}

?>