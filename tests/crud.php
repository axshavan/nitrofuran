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
	// 2.1) просто добавление
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
			echo 'test 2.1 failed in line '.__LINE__."\n";
			break;
		}
	}
	// 2.2) проверка, чего там добавилось
	$res = $DB->QueryFetched("select * from `".$table_name."`");
	if(sizeof($res) != sizeof($_records))
	{
		echo 'test 2.2 failed in line '.__LINE__."\n";
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
	// 3.1) просто выборка
	$subject_res   = $subject->read($table_name);
	$_records_copy = $_records;
	foreach($subject_res as $row)
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
		echo 'test 3.1 failed in line '.__LINE__."\n";
		break;
	}
	// 3.2) выборка с простым условием
	$subject_res = $subject->read($table_name, array('field_0' => $_records[0]['field_0']));
	foreach($subject_res as $v)
	{
		if($v['field_0'] != $_records[0]['field_0'])
		{
			echo 'test 3.2 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.3) выборка со сложным условием
	$subject_res = $subject->read($table_name, array('field_0' => array($_records[0]['field_0'], $_records[1]['field_0'])));
	foreach($subject_res as $v)
	{
		if($v['field_0'] != $_records[0]['field_0'] && $v['field_0'] != $_records[1]['field_0'])
		{
			echo 'test 3.3 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.4) выборка с сортировкой
	$subject_res = $subject->read($table_name, array(), array('field_1' => 'desc', 'field_2' => 'asc'));
	$prev_v      = array();
	foreach($subject_res as $v)
	{
		if(!sizeof($prev_v))
		{
			$prev_v = $v;
			continue;
		}
		if($prev_v['field_1'] < $v['field_1'])
		{
			echo 'test 3.4 failed in line '.__LINE__."\n";
			break 2;
		}
		elseif($prev_v['field_1'] == $v['field_1'] && $prev_v['field_2'] > $v['field_2'])
		{
			echo 'test 3.4 failed in line '.__LINE__."\n";
			break 2;
		}
		$prev_v = $v;
	}
	// 3.5) выборка с лимитом
	$subject_res = $subject->read($table_name, array(), array(), array('limit' => 30));
	if(sizeof($subject_res) != 30)
	{
		echo 'test 3.5 failed in line '.__LINE__."\n";
		break;
	}
	// 3.6) выборка с фильтрацией и сортировкой
	$subject_res = $subject->read
	(
		$table_name,
		array('field_0' => array($_records[0]['field_0'], $_records[1]['field_0'])),
		array('field_0' => 'asc')
	);
	$prev_v = array();
	foreach($subject_res as $v)
	{
		if($v['field_0'] != $_records[0]['field_0'] && $v['field_0'] != $_records[1]['field_0'])
		{
			echo 'test 3.6 failed in line '.__LINE__."\n";
			break 2;
		}
		if(!sizeof($prev_v))
		{
			$prev_v = $v;
			continue;
		}
		if($prev_v['field_0'] > $v['field_0'])
		{
			echo 'test 3.6 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.7) выборка с фильтрацией и лимитом
	$subject_res = $subject->read
	(
		$table_name,
		array('field_0' => array($_records[0]['field_0'], $_records[1]['field_0'])),
		array(),
		array('limit' => 1)
	);
	if(sizeof($subject_res) != 1)
	{
		echo 'test 3.7 failed in line '.__LINE__."\n";
		break;
	}
	if($subject_res[0]['field_0'] != $_records[0]['field_0'] && $subject_res[0]['field_0'] != $_records[1]['field_0'])
	{
		echo 'test 3.7 failed in line '.__LINE__."\n";
		break;
	}
	// 3.8) выборка с сортировкой и лимитом
	$subject_res = $subject->read
	(
		$table_name,
		array(),
		array('field_3' => 'asc'),
		array('limit' => 20)
	);
	if(sizeof($subject_res) != 20)
	{
		echo 'test 3.8 failed in line '.__LINE__."\n";
		break;
	}
	$subject_res2 = $subject->read
	(
		$table_name,
		array(),
		array('field_3' => 'asc'),
		array('limit' => 3, 'offset' => 15)
	);
	if(sizeof($subject_res2) != 3)
	{
		echo 'test 3.8 failed in line '.__LINE__."\n";
		break;
	}
	if($subject_res[15]['field_0'] != $subject_res2[0]['field_0'])
	{
		// тут может быть ложное срабатывание, если значения элементов равны
		// и они поменялись местами при сортировке. надо прогнать ещё, чтоб убедиться, что это не так
		echo 'test 3.8 failed in line '.__LINE__."\n";
		break;
	}
	// 3.9) выборка с фильтрацией, сортировкой и лимитом
	$_filter = array($_records[0]['field_2'], $_records[1]['field_2'], $_records[2]['field_2'], 'jagajaga"`\'');
	$subject_res = $subject->read
	(
		$table_name,
		array('field_2' => $_filter),
		array('field_4' => 'asc'),
		array('limit' => 10, 'offset' => 1)
	);
	if(sizeof($subject_res) > 10)
	{
		echo 'test 3.9 failed in line '.__LINE__."\n";
		break;
	}
	$prev_v = array();
	foreach($subject_res as $v)
	{
		if(!in_array($v['field_2'], $_filter))
		{
			echo 'test 3.9 failed in line '.__LINE__."\n";
			break 2;
		}
		if(!sizeof($prev_v))
		{
			$prev_v = $v;
			continue;
		}
		if($prev_v['field_4'] > $v['field_4'])
		{
			echo 'test 3.9 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.10) выборка с фильтрацией "больше"
	$subject_res = $subject->read($table_name, array('>field_0' => 'r'));
	foreach($subject_res as $v)
	{
		if($v['field_0'] <= 'r')
		{
			echo 'test 3.10 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.11) выборка с фильтрацией "меньше"
	$subject_res = $subject->read($table_name, array('<field_0' => 'g'));
	foreach($subject_res as $v)
	{
		if($v['field_0'] >= 'g')
		{
			echo 'test 3.11 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.12) выборка с фильтрацией "больше или равно"
	$subject_res = $subject->read($table_name, array('>=field_0' => 'k'));
	foreach($subject_res as $v)
	{
		if($v['field_0'] < 'k')
		{
			echo 'test 3.12 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.13) выборка с фильтрацией "меньше или равно"
	$subject_res = $subject->read($table_name, array('<=field_0' => 'k'));
	foreach($subject_res as $v)
	{
		if($v['field_0'] > 'k')
		{
			echo 'test 3.13 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.14) выборка с фильтрацией "не равно" для скалярного значения
	$subject_res = $subject->read($table_name, array('!field_0' => $_records[10]['field_0']));
	foreach($subject_res as $v)
	{
		if($v['field_0'] == $_records[10]['field_0'])
		{
			echo 'test 3.14 failed in line '.__LINE__."\n";
			break 2;
		}
	}
	// 3.15) выборка с фильтрацией "не равно" для массива
	$subject_res = $subject->read($table_name, array('!field_0' => array($_records[10]['field_0'], $_records[11]['field_0'])));
	foreach($subject_res as $v)
	{
		if($v['field_0'] == $_records[10]['field_0'] || $v['field_0'] == $_records[11]['field_0'])
		{
			echo 'test 3.15 failed in line '.__LINE__."\n";
			break 2;
		}
	}

	// 4) обновление записей
	// 4.1) обновление с простой выборкой
	$_records[0]['field_1'] = 'jagajaga';
	$_records[0]['field_2'] = '123jopa';
	if($subject->update($table_name, array('field_0' => $_records[0]['field_0']), array('field_1' => 'jagajaga', 'field_2' => '123jopa')) != 1)
	{
		// может быть ложное срабатывание в случае, если вдруг случится удачная атака на md5 :)
		echo 'test 4.1 failed in line '.__LINE__."\n";
		break;
	}
	$subject_res = $subject->read($table_name, array('field_0' => $_records[0]['field_0']));
	if($subject_res[0] != $_records[0])
	{
		echo 'test 4.1 failed in line '.__LINE__."\n";
		break;
	}
	// 4.2) обновление со сложной выборкой
	$_records[1]['field_1'] = 'jagajaga7';
	$_records[1]['field_2'] = '123jopa7';
	$_records[2]['field_1'] = 'jagajaga7';
	$_records[2]['field_2'] = '123jopa7';
	if($subject->update
	(
		$table_name,
		array('field_0' => array($_records[1]['field_0'], $_records[2]['field_0'])),
		array('field_1' => 'jagajaga7', 'field_2' => '123jopa7')
	) != 2)
	{
		// может быть ложное срабатывание в случае, если вдруг случится удачная атака на md5 :)
		echo 'test 4.2 failed in line '.__LINE__."\n";
		break;
	}
	$subject_res = $subject->read($table_name, array('field_0' => array($_records[1]['field_0'], $_records[2]['field_0'])));
	if($subject_res[0]['field_1'] != 'jagajaga7' || $subject_res[0]['field_2'] != '123jopa7')
	{
		echo 'test 4.2 failed in line '.__LINE__."\n";
		break;
	}
	if($subject_res[1]['field_1'] != 'jagajaga7' || $subject_res[1]['field_2'] != '123jopa7')
	{
		echo 'test 4.2 failed in line '.__LINE__."\n";
		break;
	}
	$res = $DB->QueryFetched("select * from `".$table_name."` where `field_0` not in ('".$_records[0]['field_0']."', '".$_records[1]['field_0']."', '".$_records[2]['field_0']."') order by `field_3` desc limit 1");
	if($res[0]['field_1'] == 'jagajaga7' || $res[0]['field_1'] == 'jagajaga')
	{
		echo 'test 4.2 failed in line '.__LINE__."\n";
		break;
	}
	if($res[0]['field_2'] == '123jopa7' || $res[0]['field_2'] == '123jopa')
	{
		echo 'test 4.2 failed in line '.__LINE__."\n";
		break;
	}

	// 5) удаление записей
	// 5.1) удаление с простым условием
	$deleted1 = $subject->delete($table_name, array('field_3' => $_records[0]['field_3']));
	if(!$deleted1)
	{
		echo 'test 5.1 failed in line '.__LINE__."\n";
		break;
	}
	$subject_res = $subject->read($table_name);
	if(sizeof($subject_res) + $deleted1 != sizeof($_records))
	{
		echo 'test 5.1 failed in line '.__LINE__."\n";
		break;
	}
	// 5.2) удаление со сложным условием
	$deleted2 = $subject->delete($table_name, array('field_4' => array($_records[1]['field_4'], $_records[2]['field_4'])));
	if(!$deleted2)
	{
		echo 'test 5.2 failed in line '.__LINE__."\n";
		break;
	}
	$subject_res = $subject->read($table_name);
	if(sizeof($subject_res) + $deleted1 + $deleted2 != sizeof($_records))
	{
		echo 'test 5.2 failed in line '.__LINE__."\n";
		break;
	}
	// 5.3) удаление всего
	$deleted3 = $subject->delete($table_name);
	if(!$deleted3 || $deleted3 != sizeof($_records) - $deleted1 - $deleted2)
	{
		echo 'test 5.3 failed in line '.__LINE__."\n";
		break;
	}
	$subject_res = $subject->read($table_name);
	if(sizeof($subject_res))
	{
		echo 'test 5.3 failed in line '.__LINE__."\n";
		break;
	}

	echo 'All tests passed';
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