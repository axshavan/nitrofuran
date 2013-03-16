<?php

echo '<pre>';

require_once($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/crud.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/db.class.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/config.php');
global $DB;
$DB      = new CDatabase();
$subject = new CRUD();

// 1) создание временной таблицы для тестирования
$table_name = 'tmp_table_'.md5(time());
$_fields    = array();
$query      = "create table `".$table_name."` (";
for($i = 0; $i < 6; $i++)
{
	$f_name      = 'field_'.$i;
	$_fields[$i] = $f_name;
	$query      .= "`".$f_name."` varchar(32),";
}
$query = substr($query, 0, strlen($query) - 1).") engine=MyISAM";
$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
if(!$DB->Query($query))
{
	die($DB->Error());
}

do
{
	// 2) добавление записей
	$_records = array();
	for($i = 0; $i < 300; $i++)
	{
		$_record = array();
		for($j = 0; $j < 6; $j++)
		{
			$_record[$_fields[$j]] = md5(rand());
		}
		$_records[] = $_record;
		if(!$subject->create($table_name, $_record))
		{
			echo 'test 2 failed in line '.__LINE__."\n";
			break;
		}
	}
	// 2.1) проверка, чего там добавилось
	$res = $DB->QueryFetched("select * from `".$table_name."`");
	if(sizeof($res) != sizeof($_records))
	{
		echo 'test 2.1 failed in line '.__LINE__."\n";
		break;
	}
	$_records_copy = $_records;
	foreach($res as $row)
	{
		foreach($_records_copy as $k => $v)
		{
			if($row == $v)
			{
				unset($_records_copy[$k]);
			}
		}
	}
	if(sizeof($_records_copy))
	{
		echo 'test 2.1 failed in line '.__LINE__."\n";
		break;
	}

	// 3) выборка записей

	// 4) обновление записей

	// 5) удаление записей
}
while(false);

// 6) удаление временной таблицы
if(!$DB->Query("drop table `".$table_name."`"))
{
	die($DB->Error());
}
$DB->Disconnect();
echo '</pre>';

?>