<?php

// 000037
// установка модуля reader, если он ещё не установлен

$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
$bReaderInstalled = isset($r['reader']);
if($bReaderInstalled)
{
	return true;
}

if(isset($_POST['install_reader']))
{
	if($_POST['install_reader'])
	{
		require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/reader.php');
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
	    Установить модуль чтения подписок &laquo;reader&raquo;?
	    <input type="hidden" value="1" name="install_reader" id="install_reader">
	    <input type="button" value="Да" onclick="document.getElementById('install_reader').value=1;document.getElementById('submit_form').submit();">
	    <input type="button" value="Нет" onclick="document.getElementById('install_reader').value=0;document.getElementById('submit_form').submit();">
	</form>
	<?
	die();
}

?>