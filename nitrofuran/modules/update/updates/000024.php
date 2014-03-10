<?php

// 000024
// установка модуля drunkeeper

$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
$bInstalled = isset($r['drunkeeper']);
if($bInstalled)
{
	return true;
}
if(isset($_POST['install_drunkeeper']))
{
	if($_POST['install_drunkeeper'])
	{
		return require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/drunkeeper.php');
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
    Установить модуль хранения статистики выпитого спиртного &laquo;drunkeeper&raquo;?
    <input type="hidden" value="1" name="install_drunkeeper" id="install_module">
    <input type="button" value="Да" onclick="document.getElementById('install_module').value=1;document.getElementById('submit_form').submit();">
    <input type="button" value="Нет" onclick="document.getElementById('install_module').value=0;document.getElementById('submit_form').submit();">
</form>
<?
	die();
}

?>