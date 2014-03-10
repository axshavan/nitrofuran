<?php

// 000004
// установка модуля kokol (если он ещё не установлен)

$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
$bInstalled = isset($r['kokol']);
if($bInstalled)
{
	return true;
}
if(isset($_POST['install_kokol']))
{
	if($_POST['install_kokol'])
	{
		return require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/kokol.php');
	}
	else
	{
		return true;
	}
}
else
{
	header("Content-Type: text/html; charset=utf-8");
	?>
<form action="/admin/?module=update&page=1&proceed" method="post" id="submit_form">
    Установить модуль хранения паролей &laquo;kokol&raquo;?
    <input type="hidden" value="1" name="install_kokol" id="install_module">
    <input type="button" value="Да" onclick="document.getElementById('install_module').value=1;document.getElementById('submit_form').submit();">
    <input type="button" value="Нет" onclick="document.getElementById('install_module').value=0;document.getElementById('submit_form').submit();">
</form>
<?
	die();
}

?>