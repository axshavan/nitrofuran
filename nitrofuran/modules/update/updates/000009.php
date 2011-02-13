<?php

// 000009
// установка модуля static (если он ещё не установлен)

$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
$bStaticInstalled = isset($r['static']);
if($bStaticInstalled)
{
	return true;
}

if(isset($_POST['install_static']))
{
	if($_POST['install_static'])
	{
		require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/static.php');
	}
}
else
{
	header("Content-Type: text/html; charset=utf-8");
	?>
	<form action="/admin/?module=update&page=1&proceed" method="post" id="submit_form">
		Установить модуль статических страниц &laquo;static&raquo;?
		<input type="hidden" value="1" name="install_static" id="install_static">
		<input type="button" value="Да" onclick="document.getElementById('install_static').value=1;document.getElementById('submit_form').submit();">
		<input type="button" value="Нет" onclick="document.getElementById('install_static').value=0;document.getElementById('submit_form').submit();">
	</form>
	<?
	die();
}

?>