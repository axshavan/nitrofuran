<?php

/*
	Обёртка для openssl, помогающая шифровать и дешифровать данные
	алгоритмом RSA. Для хранения ключей применяет временные файлы.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://www.gnu.org/licenses/gpl.html GNU GPL
	Вы можете использовать этот исходный код, вносить в него изменения,
	распространять его, делать с ним вообще всё, что хотите, при условии того,
	что вы будете сохранять ссылку на первоначального автора и сохранять код
	открытым. И автор этого кода не несёт за него никакой ответственности.
*/

class rsa
{
	// шаблоны команд openssl
	// 1024 - длина ключа, меньше 512 лучше не ставить, так как короткие ключи слабы
	protected $_commands = array(
		'genrsa' => "openssl genrsa -out %PRIVATEKEYFILE% 1024",
		'encode' => "openssl rsautl -encrypt -inkey %PRIVATEKEYFILE% -in %RANDOMFILE1% -out %RANDOMFILE2%",
		'decode' => "openssl rsautl -decrypt -inkey %PRIVATEKEYFILE% -in %RANDOMFILE1% -out %RANDOMFILE2%"
	);
	// защищённые переменные руками не трогать
	protected $key_exists           = false;
	protected $private_key_filename = '';
	protected $random_file1         = '';
	protected $random_file2         = '';
	
	/*
		Конструктор.
	*/
	public function __construct()
	{
		$this->generate_private_key_filename();
	}
	
	/*
		Деструктор.
	*/
	public function __destruct()
	{
		unlink($this->private_key_filename);
		unlink($this->random_file1);
		unlink($this->random_file2);
	}
	
	/*
		Осуществить кодирование строки. Для этого требуется ключ.
		@param  string $str данные для кодирования
		@return string
	*/
	public function encode($str)
	{
		if(!$this->key_exists)
		{
			return false;
		}
		$c = str_replace('%PRIVATEKEYFILE%', $this->private_key_filename, $this->_commands['encode']);
		$c = str_replace('%RANDOMFILE1%',    $this->random_file1,         $c);
		$c = str_replace('%RANDOMFILE2%',    $this->random_file2,         $c);
		$c = str_replace('%TEXT%',           $str,                        $c);
		file_put_contents($this->random_file1, $str);
		ob_start();
		`$c`;
		ob_get_clean();
		return file_get_contents($this->random_file2);
	}
	
	/*
		Декодировать строку. Для этого требуется ключ.
		@param  string $encoded_string закодированные данные
		@return string
	*/
	public function decode($encoded_string)
	{
		if(!$this->key_exists)
		{
			return false;
		}
		$c = str_replace('%PRIVATEKEYFILE%', $this->private_key_filename, $this->_commands['decode']);
		$c = str_replace('%RANDOMFILE1%',    $this->random_file1,         $c);
		$c = str_replace('%RANDOMFILE2%',    $this->random_file2,         $c);
		file_put_contents($this->random_file1, $encoded_string);
		ob_start();
		`$c`;
		ob_get_clean();
		return file_get_contents($this->random_file2);
	}
	
	/*
		Сгенерировать ключ.
	*/
	public function generate_key()
	{
		$c = str_replace('%PRIVATEKEYFILE%', $this->private_key_filename, $this->_commands['genrsa']);
		ob_start();
		`$c`;
		ob_end_clean();
		$this->key_exists = true;
	}
	
	/*
		Прлучить сгенерированный ключ.
		@return string ранее сгенерированный ключ
	*/
	public function get_key()
	{
		if(!file_exists($this->private_key_filename))
		{
			return false;
		}
		$text = file_get_contents($this->private_key_filename);
		$text = explode("\n", $text);
		unset($text[0]);
		unset($text[count($text) - 1]);
		return trim(implode("\n", $text), "\n");
	}
	
	/*
		Если у вас есть ключ, его можно не генерировать, а задать.
		@param string $key ключ
	*/
	public function set_key($key)
	{
		file_put_contents($this->private_key_filename, "-----BEGIN RSA PRIVATE KEY-----\n".trim($key, " \n")."\n-----END RSA PRIVATE KEY-----\n");
		$this->key_exists = true;
	}
	
	/*
		Сгенерировать случайное имя для файла, где будет хранится ключ,
		и двух файлов для буферов openssl (из командной строки большие
		объёмы данных что-то херово оно принимает).
	*/
	protected function generate_private_key_filename()
	{
		if(strlen($this->private_key_filename))
		{
			if(file_exists($this->private_key_filename))
			{
				unlink($this->private_key_filename);
			}
		}
		$this->private_key_filename = '/tmp/'.md5(time().rand(0, 10000));
		if(strlen($this->random_file1))
		{
			if(file_exists($this->random_file1))
			{
				unlink($this->random_file1);
			}
		}
		$this->random_file1 = '/tmp/'.md5(time().rand(0, 10000));
		if(strlen($this->random_file2))
		{
			if(file_exists($this->random_file2))
			{
				unlink($this->random_file2);
			}
		}
		$this->random_file2 = '/tmp/'.md5(time().rand(0, 10000));
	}
}

?>
