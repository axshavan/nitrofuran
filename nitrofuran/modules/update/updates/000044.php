<?php

// 000044
// установка модуля stamps, если он ещё не установлен

$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
$bStampsInstalled = isset($r['stamps']);
if($bStampsInstalled)
{
    return true;
}
if(isset($_POST['install_stamps']))
{
	if($_POST['install_stamps'])
	{
		return require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/stamps.php');
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
    Установить модуль хранения почтовых марок &laquo;stamps&raquo;?
    <input type="hidden" value="1" name="install_stamps" id="install_stamps">
    <input type="button" value="Да" onclick="document.getElementById('install_stamps').value=1;document.getElementById('submit_form').submit();">
    <input type="button" value="Нет" onclick="document.getElementById('install_stamps').value=0;document.getElementById('submit_form').submit();">
</form>
<?
    die();
}

?>
