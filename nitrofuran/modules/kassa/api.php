<?php

/*
	Маршрутизатор запросов к API кассы
	@author Dmitry Nikiforov <axshavan@yandex.ru>
	@license http://sam.zoy.org/wtfpl WTFPL
	This program is free software. It comes without any warranty, to
	the extent permitted by applicable law. You can redistribute it
	and/or modify it under the terms of the Do What The Fuck You Want
	To Public License, Version 2, as published by Sam Hocevar. See
	http://sam.zoy.org/wtfpl/COPYING for more details.
*/

require_once('config.php');
require_once(dirname(__FILE__).'/kassa.php');

$kassa = new CKassa();
if(!$kassa->CheckAccess($_POST['login'], $_POST['password']))
{
	error403();
}
header('Content-Type: text/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="utf-8"?>'."\n".'<reply>'."\n";
switch($_POST['method'])
{
	case 'add':
	{
		$error_code    = '';
		$errot_message = '';
		if($kassa->Add($_POST, $error_code, $error_message))
		{
			echo "\t".'<success />'."\n";
		}
		else
		{
			echo "\t".'<error code="'.$error_code.'">'.$error_message.'</error>'."\n";
		}
		break;
	}
	case 'getaccounts':
	{
		$data = $kassa->GetAccounts();
		echo "\t".'<accounts>'."\n";
		foreach($data as $v)
		{
			echo "\t\t".'<account id="'.(int)$v['id'].'" active="'.(int)$v['show'].'" default="'.(int)$v['default'].'">'.htmlspecialchars($v['name']).'</account>'."\n";
		}
		echo "\t".'</accounts>'."\n";
		break;
	}
	case 'getcurrencies':
	{
		$data = $kassa->GetCurrencies();
		echo "\t".'<currencies>'."\n";
		foreach($data as $v)
		{
			echo "\t\t".'<currency id="'.(int)$v['id'].'" default="'.(int)$v['default'].'" symbol="'.htmlspecialchars($v['symbol']).'">'.htmlspecialchars($v['name']).'</currency>'."\n";
		}
		echo "\t".'</currencies>'."\n";
		break;
	}
	case 'getoperations':
	{
		$data = $kassa->GetOperations($_POST);
		echo "\t".'<operations>'."\n";
		foreach($data as $v)
		{
			echo "\t\t".'<operation id="'.$v['id'].'">'."\n"
				."\t\t\t".'<currency id="'.$v['currency_id'].'">'.h($v['currency']).'</currency>'."\n"
				."\t\t\t".'<account id="'.$v['account_id'].'">'.h($v['account']).'</account>'."\n"
				."\t\t\t".'<comment>'.h($v['comment']).'</comment>'."\n"
				."\t\t\t".'<time timestamp="'.$v['backtime'].'">'.date('Y-m-d H:i:s', $v['backtime']).'</time>'."\n"
				."\t\t\t".'<amount>'.$v['amount'].'</amount>'."\n"
				."\t\t\t".'<optype id="'.$v['type_id'].'">'.$v['optype'].'</optype>'."\n"
				."\t\t".'</operation>'."\n";
		}
		echo "\t".'</operations>'."\n";
		break;
	}
	case 'getoptypes':
	{
		$data = $kassa->GetOptypes();
		if($_POST['group'])
		{
			echo "\t".'<optypegroups>'."\n";
			foreach($data['optypegroups'] as $v)
			{
				echo "\t\t".'<optypegroup id="'.(int)$v['id'].'" name="'.htmlspecialchars($v['name']).'">'."\n";
				foreach($data['optypes'] as $vv)
				{
					if($v['id'] == $vv['group_id'])
					{
						echo "\t\t\t".'<optype id="'.(int)$vv['id'].'" is_income="'.(int)$vv['is_income'].'">'.htmlspecialchars($vv['name']).'</optype>'."\n";
					}
				}
				echo "\t\t".'</optypegroup>'."\n";
			}
			echo "\t".'</optypegroups>'."\n";
		}
		else
		{
			echo "\t".'<optypes>'."\n";
			foreach($data['optypes'] as $v)
			{
				echo "\t\t".'<optype id="'.(int)$v['id'].'" group_id="'.(int)$v['group_id'].'" is_income="'.(int)$v['is_income'].'">'.htmlspecialchars($v['name']).'</optype>'."\n";
			}
			echo "\t".'</optypes>'."\n";
		}
		break;
	}
	default:
	{
		echo "\t".'<error code="NOT_IMPLEMENTED">Метод не реализован</error>'."\n";
		break;
	}
}
echo '</reply>';
?>