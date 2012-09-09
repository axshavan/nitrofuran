<?php

/*
	Drunkeeper plot file.
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

$img_filename = dirname(__FILE__).'/plot.jpg';
$bGenerateImg = false;
if(!file_exists($img_filename))
{
	$bGenerateImg = true;
}
else
{
	if(filemtime($img_filename) < time() - 86400)
	{
		$bGenerateImg = true;
	}
}

// генерация картинки
if($bGenerateImg)
{
	require('config.php');
	global $DB;
	
	$_drinks = array();
	$res     = $DB->Query("select * from `".DRUNKEEPER_DRINKS_TABLE."`");
	while($_r = $DB->Fetch($res))
	{
		$_drinks[$_r['id']] = $_r;
	}
	
	$_acts  = array();
	$res    = $DB->Query("select * from `".DRUNKEEPER_ACTS_TABLE."`
		where `date_drinked` > unix_timestamp() - 30 * 86400
		order by `date_drinked` desc, `id` desc");
	while($_r = $DB->Fetch($res))
	{
		$_acts[date('Yz', $_r['date_drinked'])] = array
		(
			'vol'    => $_r['volume'],
			'vol100' => round($_r['volume'] * $_drinks[$_r['drink_id']]['strength'] / 100, 2)
		);
	}
	
	$_data = array('vol' => array(), 'vol100' => array());
	for($i = 0; $i < 30; $i++)
	{
		$date  = date('M.d', time() - $i * 86400);
		$datec = date('Yz', time() - $i * 86400);
		$_data['vol'][$date]    = 0;
		$_data['vol100'][$date] = 0;
		foreach($_acts as $k => $_r)
		{
			if($datec == $k)
			{
				$_data['vol'][$date]    += $_r['vol'];
				$_data['vol100'][$date] += $_r['vol100'];
			}
		}
	}
	$_data['vol']    = array_reverse($_data['vol']);
	$_data['vol100'] = array_reverse($_data['vol100']);
	
	require_once(DOCUMENT_ROOT.'/nitrofuran/chart.class.php');
	imagejpeg
	(
		CChart::multiline_graph
		(
			array
			(
				'width'    => 850,
				'height'   => 230,
				'colors'   => array('008000', 'ff0000'),
				'bgcolor'  => 'ffffff',
				'xtick'    => 1,
				'labelmax' => true,
				'labelmin' => true,
				'grid'     => true
			),
			$_data
		),
		$img_filename,
		95
	);
}

if(!isset($result))
{
	$result = file_get_contents($img_filename);
}
header('Content-Type: image/jpeg');
echo $result;

?>