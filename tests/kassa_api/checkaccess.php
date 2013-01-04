<? header("Content-Type: text/html; charset=utf-8") ?>
<form action="checkaccess.php" method="get">
	<input type="text" name="login" id="login" value="<?= $_GET['login'] ? htmlspecialchars($_GET['login']) : 'admin' ?>"> <label for="login">login</label><br />
    <input type="text" name="password" id="password" value="<?= $_GET['password'] ? htmlspecialchars($_GET['password']) : '' ?>"> <label for="password">password</label><br />
	<input type="submit" value="Test">
</form>
<?php

if($_GET['login'])
{
	require_once($_SERVER['DOCUMENT_ROOT'].'/nitrofuran/config.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/libfunc.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/db.class.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/auth.class.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/module.class.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/te.class.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/tracer.class.php');
	require_once(DOCUMENT_ROOT.'/nitrofuran/user.class.php');
	require_once(DOCUMENT_ROOT.'nitrofuran/modules/kassa/config.php');
	require_once(DOCUMENT_ROOT.'nitrofuran/modules/kassa/kassa.php');

	global $DB;
	$DB = new CDatabase();
	$DB->Connect(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);

	echo '<pre>Проверка CKassa::ChechAccess()<br />';
	$kassa = new CKassa();
	echo $kassa->CheckAccess($_GET['login'], $_GET['password']) ? 'Доступ есть' : 'Доступа нет';
	echo '<br />Проверка доступа через API<br />';
	$curl = curl_init($_SERVER['SERVER_NAME'].'/kassa/api/');
	curl_setopt($curl, CURLOPT_POSTFIELDS, 'login='.$_GET['login'].'&password='.$_GET['password']);
	ob_start();
	curl_exec($curl);
	$curl_res = ob_get_clean();
	curl_close($curl);
	echo htmlspecialchars($curl_res).'</pre>';
}

?>