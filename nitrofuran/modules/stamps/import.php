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

$iProcess = 0;
require_once(dirname(__FILE__).'/config.php');
if(isset($_SERVER['PWD'])) {
	define('DOCUMENT_ROOT', dirname(__FILE__).'/../../..');
	require_once(dirname(__FILE__).'/../../config.php');
	require_once(dirname(__FILE__).'/../../libfunc.php');
	require_once(dirname(__FILE__).'/../../db.class.php');
	$DB = new CDatabase();
	$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
	set_time_limit(0);
	$cmd = 'cd '.DOCUMENT_ROOT.'/tmp && $(which unzip) -u stamps.ods';
	shell_exec($cmd);
	$iProcess = 2;
	$method = 'string';
}
if(isset($_POST['import']) && $_POST['import'])
{
	set_time_limit(600);
	move_uploaded_file($_FILES['file']['tmp_name'], DOCUMENT_ROOT.'/tmp/stamps');
	$cmd = 'cd '.DOCUMENT_ROOT.'/tmp && $(which unzip) -u stamps';
	shell_exec($cmd);
	$iProcess = 1;
	$method = 'regexp';
}
if($iProcess)
{
	if(!file_exists(DOCUMENT_ROOT.'/tmp/content.xml'))
	{
		clean_tmp_dir();
		error500();
	}
	if($iProcess == 2)
	{
		echo "content.xml exists, xml loading with method '".$method."'...";
	}
	require_once(DOCUMENT_ROOT.'/nitrofuran/xml.class.php');
	$xmlparser = new CXMLParser();
	$xmlparser->load_from_file(DOCUMENT_ROOT.'/tmp/content.xml', $method);
	if($iProcess == 2)
	{
		echo "done\ngetting stamps as array...";
	}
	$db = $xmlparser->get_as_array();
	if($iProcess == 2)
	{
		echo "done\nworking with database...\n";
	}
	unset($xmlparser);
	clean_tmp_dir();
	if(!isset($db[0]['content'])) {
		error500();
	}
	$db = $db[0]['content']; // office:document-content
	if(!isset($db[3]['content'])) {
		error500();
	}
	$db = $db[3]['content']; // office:body
	if(!isset($db[0]['content'])) {
		error500();
	}
	$db = $db[0]['content']; // office:spreadsheet
	$table_id = "";
	$DB->Query("truncate table ".STAMPS_TABLE);
	if($iProcess == 2)
	{
		echo "table ".STAMPS_TABLE." truncated\n";
	}
	foreach($db as $table) // table:table
	{
		$table_id = isset($table['properties']['table:name']) ? $table['properties']['table:name'] : '';
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
				$text = isset($cell['content'][0]['content']) ? $cell['content'][0]['content'] : ''; // text:p
				if($text)
				{
					$_data[$table_id][$row_id][] = $text;
				}
			}
			if(isset($_data[$table_id][$row_id]) && sizeof($_data[$table_id][$row_id]))
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
							)"
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
	if($iProcess == 1)
	{
		redirect('/stamps');
	}
	elseif($iProcess == 2) {
		echo "job finished\n";
	}
}
else
{
	$tplengine = new CTemplateEngine('stamps');
	$tplengine->template('import.tpl');
}
