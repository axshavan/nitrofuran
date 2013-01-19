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

$_acts   = array();
$_stats  = array();
$res     = $DB->Query("select * from `".DRUNKEEPER_ACTS_TABLE."` order by `date_drinked` desc, `id` desc");
$count   = 0;
$_last3m = array();
$n_cur   = (int)date('Ym', time() - 86400 * 180); // сейчас минут полгода
while($_r = $DB->Fetch($res))
{
	if($count < 75)
	{
		$_acts[$_r['id']] = $_r;
	}
	$_stats['allvolume']  += $_r['volume'];
	$_stats['40volume']   += $_r['volume'] / (40 / $_drinks[$_r['drink_id']]['strength']);
	$_stats['100volume']  += $_r['volume'] / (95.6 / $_drinks[$_r['drink_id']]['strength']);
	$_stats['median']     += $_r['volume'] * $_drinks[$_r['drink_id']]['strength'];
	$_stats['volume_dtype'][$_drinks[$_r['drink_id']]['type_id']] += $_r['volume'];
	$_stats['volume_d'][$_r['drink_id']] += $_r['volume'];
	$n = (int)date('Ym', $_r['date_drinked']);
	if($n > $n_cur)
	{
		$_last3m[$n]['allvolume'] += $_r['volume'];
		$_last3m[$n]['40volume']      += $_r['volume'] / (40 / $_drinks[$_r['drink_id']]['strength']);
		$_last3m[$n]['100volume']     += $_r['volume'] / (95.6 / $_drinks[$_r['drink_id']]['strength']);
		$_last3m[$n]['volume_dtype'][$_drinks[$_r['drink_id']]['type_id']] += $_r['volume'];
	}
	$count++;
}
$_stats['median'] = $_stats['median'] / $_stats['allvolume'];
arsort($_stats['volume_dtype']);
arsort($_stats['volume_d']);

$tplengine->assign('_drinks',      $_drinks);
$tplengine->assign('_drink_types', $_drink_types);
$tplengine->assign('_acts',        $_acts);
$tplengine->assign('_stats',       $_stats);
$tplengine->assign('_last3m',      $_last3m);

$tplengine->template('index.tpl');

?>