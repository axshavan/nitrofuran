<? header("Content-Type: text/html; charset=utf-8") ?>
<form action="get_data.php" method="get">
    <input type="text" name="login" id="login" value="<?= $_GET['login'] ? htmlspecialchars($_GET['login']) : 'admin' ?>"> <label for="login">login</label><br />
    <input type="text" name="password" id="password" value="<?= $_GET['password'] ? htmlspecialchars($_GET['password']) : '' ?>"> <label for="password">password</label><br />
    <input type="submit" value="Test">
</form>
<?php

if($_GET['login'])
{
	echo '<pre><br />Метод GetAccount - получение списка счетов в кассе<br />';
	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getaccounts');
	ob_start();
	curl_exec($curl);
	$curl_res = ob_get_clean();
	curl_close($curl);
	echo htmlspecialchars($curl_res).'</pre>';

	echo '<pre><br />Метод GetCurrencies - получение списка валют в кассе<br />';
	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getcurrencies');
	ob_start();
	curl_exec($curl);
	$curl_res = ob_get_clean();
	curl_close($curl);
	echo htmlspecialchars($curl_res).'</pre>';

	echo '<pre><br />Метод GetOptypes - получение списка типов операций в кассе простым списком<br />';
	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getoptypes&group=0');
	ob_start();
	curl_exec($curl);
	$curl_res = ob_get_clean();
	curl_close($curl);
	echo htmlspecialchars($curl_res).'</pre>';

	echo '<pre><br />Метод GetOptypes - получение списка типов операций в кассе с группировкой<br />';
	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password'].'&method=getoptypes&group=1');
	ob_start();
	curl_exec($curl);
	$curl_res = ob_get_clean();
	curl_close($curl);
	echo htmlspecialchars($curl_res).'</pre>';
}

?>