<?php

/*
	Drunkeeper index file.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require('config.php');
global $DB;
$tplengine = new CTemplateEngine('drunkeeper');

$_drink_types = array();
$res          = $DB->Query("select * from `".DRUNKEEPER_DRINKTYPES_TABLE."`");
while($_r = $DB->Fetch($res))
{
	$_drink_types[$_r['id']] = $_r;
}

$_drinks = array();
$res     = $DB->Query("select * from `".DRUNKEEPER_DRINKS_TABLE."`");
while($_r = $DB->Fetch($res))
{
	$_drinks[$_r['id']] = $_r;
	$_drink_types[$_r['type_id']]['drinks'][$_r['id']] = $_r;
}

$_acts  = array();
$_stats = array();
$res    = $DB->Query("select * from `".DRUNKEEPER_ACTS_TABLE."` order by `date_drinked` desc, `id` desc");
$limit  = 0;
while($_r = $DB->Fetch($res))
{
	if($limit < 50)
	{
		$_acts[$_r['id']] = $_r;
	}
	$_stats['allvolume']  += $_r['volume'];
	$_stats['40volume']   += $_r['volume'] / (40 / $_drinks[$_r['drink_id']]['strength']);
	$_stats['100volume']  += $_r['volume'] / (95.6 / $_drinks[$_r['drink_id']]['strength']);
	$_stats['volume_dtype'][$_drinks[$_r['drink_id']]['type_id']] += $_r['volume'];
	$_stats['volume_d'][$_r['drink_id']] += $_r['volume'];
	$limit++;
}
arsort($_stats['volume_dtype']);
arsort($_stats['volume_d']);

$tplengine->assign('_drinks',      $_drinks);
$tplengine->assign('_drink_types', $_drink_types);
$tplengine->assign('_acts',        $_acts);
$tplengine->assign('_stats',       $_stats);

$tplengine->template('index.tpl');

?>