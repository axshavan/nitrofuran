<?php

/*
	Более навороченная и чуть менее простая в эксплуатации замена для mail()
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://www.gnu.org/licenses/gpl.html GNU GPL
	Вы можете использовать этот исходный код, вносить в него изменения,
	распространять его, делать с ним вообще всё, что хотите, при условии того,
	что вы будете сохранять ссылку на первоначального автора и сохранять код
	открытым. И автор этого кода не несёт за него никакой ответственности.
*/

class mail_sender
{
	public static $last_error = ''; // текст последней ошибки
	public static $smtp_log   = ''; // лог общения с SMTP-сервером
	
	protected static $_auth_types = false;
	
	/*
		Послать текстовое сообщение.
		@param string $email_from от кого
		@param string $email_to   кому
		@param string $subject    тема сообщения
		@param string $text       текст сообщения
		@return bool success
	*/
	public static function Send($email_from, $email_to, $subject, $text)
	{
		return mail_sender::SendComplicated($email_from, $email_to, $subject, array(array('CONTENT' => $text)));
	}
	
	/*
		Послать текстовое сообщение (можно с вложениями).
		@param string $email_from от кого
		@param string $email_to   кому
		@param string $subject    тема сообщения
		@param array  $_bodies    mime-куски сообщения. Имеет следующий формат:
			array(
				array(
					'CONTENT_TYPE' => тип содержимого (если не указано, то text/plain)
					'CONTENT_TRANSFER_ENCODING' => шифрование пересылаемого сообщения
						(например, base64), не обязательно
					'CONTENT_DISPOSITION' => если это приложение к письму, то
						attachment; filename=example.file
					'CONTENT' => само содержимое файла. Не то, чтоб совсем
						обязательно, но крайне желательно
				),
				...
			)
		@param  string $bcc кому скрытая копия
		@return bool   success
	*/
	public static function SendComplicated($email_from, $email_to, $subject, $_bodies = array(), $bcc = '')
	{
		$smtp_server   = ini_get('SMTP');
		$smtp_port     = ini_get('smtp_port');
		$server_name   = '';
		$auth          = false;
		$auth_password = '';
		
		if(!count($_bodies))     return false;
		if(!strlen($email_from)) return false;
		if(!strlen($email_to))   return false;
		$mailText   = '';
		$mailHeader =
			"From: ".$email_from."\n".
			"Reply-To: ".$email_from."\n";
		if(strlen($bcc))
		{
			$mailHeader .= "Bcc: ".$bcc."\n";
		}
		if(count($_bodies) > 1)
		{
			// это одновременно флаг, что письмо состоит из нескольких кусков
			$mime_boundary = '==Multipart_Boundary_X'.md5(time()).'X';
			$mailHeader .=
				"Content-Transfer-Encoding: 8bit\n".
				"Content-Type: multipart/mixed;\n boundary=\"".$mime_boundary."\"\n".
				"MIME-Version: 1.0\n";
		}
		else
		{
			$mime_boundary = '';
		}
		$mailHeader .=
			"To: ".$email_to."\n".
			"Subject: ".mail_sender::mail_subject_RFC1522($subject)."\n";
		$mailRcpt = explode(',', $email_to);
		if(strlen($bcc))
		{
			$mailRcpt = array_merge($mailRcpt, explode(',', $bcc));
		}
		// каждый кусок письма оборачиваем заголовками и цепляем к основному тексту
		foreach($_bodies as $part)
		{
			if(strlen($mime_boundary))
			{
				$mailText .= "\n".'--'.$mime_boundary."\n";
			}
			if(strlen($part['CONTENT_TYPE']))
			{
				$mailText .= 'Content-Type: '.$part['CONTENT_TYPE']."\n";
			}
			else
			{
				$mailText .= "Content-Type: text/html; charset=windows-1251\n";
			}
			if(strlen($part['CONTENT_TRANSFER_ENCODING']))
			{
				$mailText .= "Content-Transfer-Encoding: ".$part['CONTENT_TRANSFER_ENCODING']."\n";
			}
			if(strlen($part['CONTENT_DISPOSITION']))
			{
				$mailText .= "Content-Disposition: ".$part['CONTENT_DISPOSITION']."\n";
			}
			if(strlen($part['CONTENT']))
			{
				$mailText .= "\n".$part['CONTENT'];
			}
		}
		do
		{
			// понеслась!
			$result = false;
			$errstr = '';
			$errno  = 0;
			$f = fsockopen($smtp_server, $smtp_port, $errno, $errstr, 5);
			if(!$f)
			{
				mail_sender::$last_error = $errno.' ('.$errstr.')';
				break;
			}
			mail_sender::$smtp_log .= "socket opened\n";
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '220')
			{
				// SMTP-сервер не готов нас обслужить
				mail_sender::$last_error = $s;
				break;
			}
			if($auth)
			{
				$c = "EHLO ".$server_name."\n";
				mail_sender::$_auth_types = '';
				mail_sender::$smtp_log   .= "<- ".$c."\n";
				fputs($f, $c);
				// определим список расширений SMTP, поддерживаемых сервером
				while(true)
				{
					$s = fgets($f);
					mail_sender::$smtp_log .= "-> ".$s."\n";
					if(substr($s, 4, 4) == 'AUTH')
					{
						mail_sender::$_auth_types .= ' '.trim(substr($s, 9)).' ';
					}
					if(substr($s, 4, 8) == 'STARTTLS')
					{
						mail_sender::$_auth_types .= ' STARTTLS ';
					}
					if(substr($s, 0, 4) == '250 ')
					{
						// последнее расширение
						break;
					}
					if(substr($s, 0, 3) != '250')
					{
						// что-то не так
						break 2;
					}
				}
				mail_sender::$_auth_types = str_replace('  ', ' ', trim(mail_sender::$_auth_types));
				mail_sender::$_auth_types = explode(' ', mail_sender::$_auth_types);
			}
			else
			{
				// поздороваемся
				$c = "HELO ".$server_name."\n";
				mail_sender::$smtp_log .= "<- ".$c."\n";
				fputs($f, $c);
				$s = fgets($f);
				mail_sender::$smtp_log .= "-> ".$s."\n";
				if(substr($s, 0, 3) != '250')
				{
					// нас видеть не рады
					mail_sender::$last_error = $s;
					break;
				}
			}
			if(count(mail_sender::$_auth_types))
			{
				if(!mail_sender::auth(&$f, $email_from, $auth_password))
				{
					// что-то было не так с авторизацией, но мы попробуем
					// херануть письмецо на шару, вдруг прокатит
				}
			}
			// почта от кого
			$c = "MAIL FROM: <".$email_from.">\n";
			mail_sender::$smtp_log .= "<- ".htmlspecialchars($c)."\n";
			fputs($f, $c);
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '250')
			{
				// ошибка
				mail_sender::$last_error = $s;
				break;
			} 
			// список реципиентов (почта кому)
			foreach($mailRcpt as $rcpt)
			{
				$rcpt = trim($rcpt);
				if(strlen($rcpt))
				{
					$c = "RCPT TO: <".$rcpt.">\n";
					mail_sender::$smtp_log .= "<- ".htmlspecialchars($c)."\n";
					fputs($f, $c);
					$s = fgets($f);
					mail_sender::$smtp_log .= "-> ".$s."\n";
					// если тут будет неправильный почтовый адрес - похер,
					// письмо пойдёт на остальные. Если правильных адресов
					// не будет вообще, сервер нам об этом ниже сообщит
				}
			}
			// тело письма
			$c = "DATA\n";
			mail_sender::$smtp_log .= "<- ".$c."\n";
			fputs($f, $c);
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '354')
			{
				// сервер не подтверждает готовность принять тело письма
				mail_sender::$last_error = $s;
				break;
			}
			mail_sender::$smtp_log .= "<- ".$mailHeader."\n";
			fputs($f, $mailHeader);
			mail_sender::$smtp_log .= "<- ".$mailText."\n";
			fputs($f, $mailText);
			// конец письма
			$c = "\n.\n";
			mail_sender::$smtp_log .= "<- ".$c."\n";
			fputs($f, $c);
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '250')
			{
				// письмо не отправилось почему-то
				mail_sender::$last_error = $s;
				break;
			}
			// чао бамбина
			$c = "QUIT\n";
			mail_sender::$smtp_log .= "<- ".$c."\n";
			fputs($f, $c);
			fclose($f);
			$result = true;
		}
		while(false);
		return $result;
	}
	
	/*
		Авторизация.
	*/
	protected static function auth(&$f, $login, $password)
	{
		// авторизация типа LOGIN
		if(in_array('LOGIN', mail_sender::$_auth_types))
		{
			$c = "AUTH LOGIN\n";
			mail_sender::$smtp_log .= "<- ".$c."\n";
			fputs($f, $c);
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '334')
			{
				// ошибка авторизации
				mail_sender::$last_error = $s;
				return false;
			}
			$c = base64_encode($login)."\n";
			mail_sender::$smtp_log .= "<- ".$c."\n";
			fputs($f, $c);
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '334')
			{
				// ошибка авторизации
				mail_sender::$last_error = $s;
				return false;
			}
			$c = base64_encode($password)."\n";
			mail_sender::$smtp_log .= "<- ".$c."\n";
			fputs($f, $c);
			$s = fgets($f);
			mail_sender::$smtp_log .= "-> ".$s."\n";
			if(substr($s, 0, 3) != '235')
			{
				// логин или пароль неправильные
				mail_sender::$last_error = $s;
				return false;
			}
			return false;
		}
	}
	
	/*
		Зафигачить заголовок сообщения в соответствии с RFC 1522.
		@param  string $str исходная тема письма
		@return string      зафигаченная тема письма
	*/
	protected static function mail_subject_RFC1522($str)
	{
		$result = '=?WINDOWS-1251?B?'.base64_encode($str).'?=';
		// необходимо учесть требованиe RFC-1522 о длине закодированной строки, которая
		// не должна превышать 75 символов
		$parts = 1;
		$first_part_ln = strlen($result);
		while($first_part_ln > 75)
		{
			$result = '';
			$parts++;
			$first_part_ln = strlen(base64_encode(substr(
				$str, 0, IntVal(floor(strlen($str) / $mailSubjParts))
			)));
			$symbols_shift = 0;
			for($i = 0; $i < $parts; $i++)
			{
				// мозгодробительная ловля символов в случае, если получается нецелое их число
				// по-моему, тут до сих пор что-то иногда работает не так
				$sub_ln    = floor(strlen($str) / $parts);
				$sub_start = floor(strlen($str) / $parts) * $i + $symbols_shift;
				if((strlen($mailTemplate['SUBJECT']) / $mailSubjParts) - $sub_ln >= 0.5 && !($i % 2))
				{
					$symbols_shift++;
					$sub_ln++;
				}
				$result .= '=?WINDOWS-1251?B?'.base64_encode(substr($str, $sub_start, $sub_ln)).'?='."\n ";
			}
		}
		return $result;
	}
}

?>