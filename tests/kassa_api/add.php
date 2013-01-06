<? header("Content-Type: text/html; charset=utf-8") ?>
<form action="add.php" method="get">
    <input type="text" name="login" id="login" value="<?= $_REQUEST['login'] ? htmlspecialchars($_REQUEST['login']) : 'admin' ?>"> <label for="login">login</label><br />
    <input type="text" name="password" id="password" value="<?= $_REQUEST['password'] ? htmlspecialchars($_REQUEST['password']) : '' ?>"> <label for="password">password</label><br />
    <input type="submit" value="Test">
</form>
<?php

if($_POST['add'])
{
	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	$query = 'login='.$_POST['login']
		.'&password='.$_POST['password']
		.'&account='.$_POST['account']
		.'&method=add'
		.'&currency='.$_POST['currency']
		.'&optype='.$_POST['optype_'.$_POST['optypegroup']]
		.'&comment='.$_POST['comment']
		.'&amount='.$_POST['amount']
	;
	curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
	ob_start();
	curl_exec($curl);
	$curl_res = simplexml_load_string(ob_get_clean());
	curl_close($curl);
	if($curl_res->error)
	{
		echo 'Ошибка.<br />Код ошибки: '.((string)$curl_res->error->attributes()->code).'<br />Текст ошибки: '.((string)$curl_res->error);
	}
	elseif($curl_res->success)
	{
		echo 'Операция добавлена. <a href="get_operations.php?login='.$_POST['login'].'&password='.$_POST['password'].'&filter=1">Перейти к списку</a>';
	}

}
elseif($_REQUEST['login'])
{
	$_accounts   = array();
	$_currencies = array();
	$_optypesg   = array();

	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getaccounts');
	ob_start();
	curl_exec($curl);
	$curl_res = simplexml_load_string(ob_get_clean());
	curl_close($curl);
	foreach($curl_res->accounts->account as $v)
	{
		$_accounts[(int)$v->attributes()->id] = array
		(
			'default' => (int)$v->attributes()->default,
			'active'  => (int)$v->attributes()->active,
			'name'    => (string)$v
		);
	}

	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getcurrencies');
	ob_start();
	curl_exec($curl);
	$curl_res = simplexml_load_string(ob_get_clean());
	curl_close($curl);
	foreach($curl_res->currencies->currency as $v)
	{
		$_currencies[(int)$v->attributes()->id] = array
		(
			'default' => (int)$v->attributes()->default,
			'symbol'  => (string)$v->attributes()->symbol,
			'name'    => (string)$v
		);
	}

	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getoptypes&group=1');
	ob_start();
	curl_exec($curl);
	$curl_res = simplexml_load_string(ob_get_clean());
	curl_close($curl);
	foreach($curl_res->optypegroups->optypegroup as $v)
	{
		$_optypes = array();
		foreach($v->optype as $vv)
		{
			$_optypes[(int)$vv->attributes()->id] = array
			(
				'name'      => (string)$vv,
				'is_income' => (int)$vv->attributes()->is_income
			);
		}
		$_optypesg[(int)$v->attributes()->id] = array
		(
			'name'    => (string)$v->attributes()->name,
			'optypes' => $_optypes
		);
	}
	?>
	<form action="add.php" method="post">
	    <input type="hidden" name="login" value="<?= htmlspecialchars($_REQUEST['login']) ?>">
	    <input type="hidden" name="password" value="<?= htmlspecialchars($_REQUEST['password']) ?>">
	    <input type="hidden" name="add" value="1">
	    <select name="account" id="account">
	        <option value="0">не задан</option>
			<? foreach($_accounts as $k => $v): ?>
	            <option value="<?= (int)$k ?>" <?= $_GET['account'] == $k ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
			<? endforeach; ?>
	    </select> <label for="account">Счёт</label><br />
	    <select name="currency" id="currency">
	        <option value="0">не задан</option>
			<? foreach($_currencies as $k => $v): ?>
	            <option value="<?= (int)$k ?>" <?= $_GET['currency'] == $k ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
			<? endforeach; ?>
	    </select> <label for="currency">Валюта</label><br />
        <input type="text" name="amount" id="amount"> <label for="amount">Сумма</label><br />
		<input type="text" name="comment" id="comment"> <label for="comment">Комментарий</label><br />
	    <select name="optypegroup" id="optypegroup">
			<? foreach($_optypesg as $k => $v): ?>
	            <option value="<?= (int)$k ?>" <?= $_GET['optypegroup'] == $k ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
			<? endforeach; ?>
	    </select> <label for="optypegroup">Группа типов</label><br />
	    Типы операций по группам:<br />
		<? foreach($_optypesg as $k => $v): ?>
	        <select name="optype_<?= (int)$k ?>" id="optype_<?= (int)$k ?>">
	            <option value="0">Все</option>
				<? foreach($v['optypes'] as $kk => $vv): ?>
	                <option value="<?= (int)$kk ?>" <?= $_GET['optype_'.$k] == $kk ? 'selected="selected"' : '' ?>><?= htmlspecialchars($vv['name']) ?></option>
				<? endforeach; ?>
	        </select> <label for="optype_<?= (int)$k ?>"><?= htmlspecialchars($v['name']) ?></label><br />
		<? endforeach; ?>
	    <br />
	    <input type="submit" value="Test">
	</form>
	<?
}

?>