<?php

/**
 * Импорт марок
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

$bProcess = FALSE;
if(isset($_SERVER['PWD'])) {
	set_time_limit(0);
	$cmd = 'cd '.DOCUMENT_ROOT.'/tmp && $(which unzip) -u stamps.ods';
	shell_exec($cmd);
	$bProcess = TRUE;
}
if($_POST['import'])
{
	set_time_limit(600);
	move_uploaded_file($_FILES['file']['tmp_name'], DOCUMENT_ROOT.'/tmp/stamps');
	$cmd = 'cd '.DOCUMENT_ROOT.'/tmp && $(which unzip) -u stamps';
	shell_exec($cmd);
	$bProcess = TRUE;
}
if($bProcess)
{
	require_once(dirname(__FILE__).'/config.php');
	if(!file_exists(DOCUMENT_ROOT.'/tmp/content.xml'))
	{
		clean_tmp_dir();
		error500();
	}
	require_once(DOCUMENT_ROOT.'/nitrofuran/xml.class.php');
	$xmlparser = new CXMLParser();
	$xmlparser->load_from_file(DOCUMENT_ROOT.'/tmp/content.xml', 'regexp');
	$db = $xmlparser->get_as_array();
	unset($xmlparser);
	clean_tmp_dir();
	$db       = $db[0]['content']; // office:document-content
	$db       = $db[3]['content']; // office:body
	$db       = $db[0]['content']; // office:spreadsheet
	$table_id = "";
	$DB->Query("truncate table ".STAMPS_TABLE);
	foreach($db as $table) // table:table
	{
		$table_id = $table['properties']['table:name'];
		if($table['tag'] != 'table:table')
		{
			continue;
		}
		$row_id = 0;
		foreach($table['content'] as $row) // table:table-row
		{
			if($row['tag'] != 'table:table-row')
			{
				continue;
			}
			foreach($row['content'] as $cell) // table:table-cell
			{
				if(!$row_id)
				{
					break;
				}
				$text = $cell['content'][0]['content']; // text:p
				if($text)
				{
					$_data[$table_id][$row_id][] = $text;
				}
			}
			if(sizeof($_data[$table_id][$row_id]))
			{
				if(sizeof($_data[$table_id][$row_id]) > 5)
				{
					$country = $_data[$table_id][$row_id][0];
					$nominal = $_data[$table_id][$row_id][1];
					$year    = $_data[$table_id][$row_id][2];
					$name    = $_data[$table_id][$row_id][3];
					$slaked  = strpos($_data[$table_id][$row_id][4], 'да') !== false;
					$book_id = $_data[$table_id][$row_id][5];
				}
				else
				{
					$country = $table_id;
					$nominal = $_data[$table_id][$row_id][0];
					$year    = $_data[$table_id][$row_id][1];
					$name    = $_data[$table_id][$row_id][2];
					$slaked  = strpos($_data[$table_id][$row_id][3], 'да') !== false;
					$book_id = $_data[$table_id][$row_id][4];
				}
				if($row_id)
				{
					// добавление записи в таблицу
					if
					(
						!$DB->Query
						(
							"insert into ".STAMPS_TABLE." (`name`, `country`, `year`, `slaked`, `nominal`, `book_id`)
							values
							(
								'".addslashes($name)."',
								'".addslashes($country)."',
								'".addslashes($year)."',
								'".(int)$slaked."',
								'".addslashes($nominal)."',
								'".(int)$book_id."'
							)",
							$cid
						)
					)
					{
						clean_tmp_dir();
						error500();
					}
				}
			}
			$row_id++;
		}
	}
	unset($db);
	redirect('/stamps');
}
else
{
	$tplengine = new CTemplateEngine('stamps');
	$tplengine->template('import.tpl');
}

?>