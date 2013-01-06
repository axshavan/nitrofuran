<? header("Content-Type: text/html; charset=utf-8") ?>
<form action="get_operations.php" method="get">
    <input type="text" name="login" id="login" value="<?= $_GET['login'] ? htmlspecialchars($_GET['login']) : 'admin' ?>"> <label for="login">login</label><br />
    <input type="text" name="password" id="password" value="<?= $_GET['password'] ? htmlspecialchars($_GET['password']) : '' ?>"> <label for="password">password</label><br />
    <input type="submit" value="Test">
</form>
<?php

if($_GET['login'])
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
	<form action="get_operations.php" method="get">
	    <input type="hidden" name="login" value="<?= htmlspecialchars($_GET['login']) ?>">
	    <input type="hidden" name="password" value="<?= htmlspecialchars($_GET['password']) ?>">
		<input type="hidden" name="filter" value="1">
		<input type="text" name="datefrom" id="datefrom" value="<?= $_GET['datefrom'] ? htmlspecialchars($_GET['datefrom']) : date('Y-m-d H:i:s',  time() - 7 * 86400) ?>"> <label for="datefrom">Дата от</label><br />
        <input type="text" name="dateto" id="dateto" value="<?= $_GET['dateto'] ? htmlspecialchars($_GET['dateto']) : date('Y-m-d H:i:s') ?>"> <label for="dateto">Дата до</label><br />
		<select name="account" id="account">
            <option value="0">Все</option>
			<? foreach($_accounts as $k => $v): ?>
				<option value="<?= (int)$k ?>" <?= $_GET['account'] == $k ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
			<? endforeach; ?>
		</select> <label for="account">Счёт</label><br />
		<select name="currency" id="currency">
            <option value="0">Все</option>
			<? foreach($_currencies as $k => $v): ?>
                <option value="<?= (int)$k ?>" <?= $_GET['currency'] == $k ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v['name']) ?></option>
			<? endforeach; ?>
		</select> <label for="currency">Валюта</label><br />
		<select name="optypegroup" id="optypegroup">
            <option value="0">Все</option>
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
	if($_GET['filter'])
	{
		$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
		$query = 'login='.$_GET['login']
			.'&password='.$_GET['password']
			.'&method=getoperations'
			.'&date_start='.$_GET['datefrom']
			.'&date_end='.$_GET['dateto']
			.'&account='.$_GET['account']
			.'&currency='.$_GET['currency']
			.'&optypegroup='.$_GET['optypegroup']
			.'&optype='.(int)$_GET['optype_'.$_GET['optypegroup']];

		curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
		ob_start();
		curl_exec($curl);
		$curl_res = ob_get_clean();
		curl_close($curl);
		echo '<pre>';
		print_r(htmlspecialchars($curl_res));
		echo '</pre>';
		$curl_res = simplexml_load_string($curl_res);


		echo '<table>
			<tr>
				<th>id</th>
				<th>amount</th>
				<th>time (timestamp)</th>
				<th>currency</th>
				<th>account</th>
				<th>optype</th>
				<th>comment</th>
			</tr>';
		foreach($curl_res->operations->operation as $v)
		{
			?>
			<tr>
				<td><?= (int)$v->attributes()->id ?></td>
				<td><?= (float)$v->amount ?></td>
				<td><?= htmlspecialchars((string)$v->time) ?> (<?= (int)$v->time->attributes()->timestamp ?>)</td>
                <td><?= htmlspecialchars((string)$v->currency) ?> (<?= (int)$v->currency->attributes()->id ?>)</td>
                <td><?= htmlspecialchars((string)$v->account) ?> (<?= (int)$v->account->attributes()->id ?>)</td>
                <td><?= htmlspecialchars((string)$v->optype) ?> (<?= (int)$v->optype->attributes()->id ?>)</td>
				<td><?= htmlspecialchars((string)$v->comment) ?></td>
			</tr>
			<?
		}
		echo '<table>';
	}
}

?>