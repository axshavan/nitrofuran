<?php

/**
 * Главный файл модуля stamps
 *
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

require_once(dirname(__FILE__).'/config.php');

// заголовки таблицы
$_headers = array
(
	array
	(
		'code' => 'country',
		'name' => 'Страна'
	),
	array
	(
		'code' => 'nominal',
		'name' => 'Номинал'
	),
	array
	(
		'code' => 'year',
		'name' => 'Год'
	),
	array
	(
		'code' => 'name',
		'name' => 'Внешний вид'
	),
	array
	(
		'code'    => 'slaked',
		'name'    => 'Гашёная',
		'boolean' => true
	),
	array
	(
		'code' => 'book_id',
		'name' => 'Книжка'
	)
);

// выборка из базы данных
require_once(DOCUMENT_ROOT.'/nitrofuran/crud.class.php');
$crud = new CRUD();

$_sort = array();
if($_REQUEST['sort'])
{
	$_sort[$_REQUEST['sort']] = $_REQUEST['dir'] == 'desc' ? 'desc' : 'asc';
}
$filter_string = "";
$_filter       = array();
if($_REQUEST['country'])
{
	$filter_string .= 'country='.htmlspecialchars($_REQUEST['country']).'&';
	$_filter['country'] .= $_REQUEST['country'];
}
if($_REQUEST['year'])
{
	$filter_string .= 'year='.htmlspecialchars($_REQUEST['year']).'&';
	$_filter['year'] .= $_REQUEST['year'];
}
if($_REQUEST['book_id'])
{
	$filter_string .= 'year='.htmlspecialchars($_REQUEST['book_id']).'&';
	$_filter['book_id'] .= $_REQUEST['book_id'];
}
$_data      = $crud->read(STAMPS_TABLE, $_filter, $_sort);
$_alldata   = $crud->read(STAMPS_TABLE);
$_countries = array();
$_years     = array();
$_book_ids  = array();
foreach($_alldata as $row)
{
	$_countries[$row['country']] = $row['country'];
	$_years[$row['year']]        = $row['year'];
	$_book_ids[$row['book_id']]  = $row['book_id'];
}
sort($_countries);
rsort($_years);
sort($_book_ids);

$tplengine = new CTemplateEngine('stamps');
$tplengine->assign('_book_names', unserialize(get_param('stamps', 'books')));
$tplengine->assign('_book_ids', $_book_ids);
$tplengine->assign('_countries', $_countries);
$tplengine->assign('_years', $_years);
$tplengine->assign('_data', $_data);
$tplengine->assign('_headers', $_headers);
$tplengine->template('index.tpl');

?>