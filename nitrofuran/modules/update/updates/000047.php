<?php

// 000047
// установка модуля blog, если он ещё не установлен

$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
$bStampsInstalled = isset($r['blog']);
if($bStampsInstalled)
{
	return true;
}

if(isset($_POST['install_blog']))
{
	if($_POST['install_blog'])
	{
		return require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/blog.php');
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
    Установить модуль ведения блога &laquo;blog&raquo;?
    <input type="hidden" value="1" name="install_blog" id="install_blog">
    <input type="button" value="Да" onclick="document.getElementById('install_blog').value=1;document.getElementById('submit_form').submit();">
    <input type="button" value="Нет" onclick="document.getElementById('install_blog').value=0;document.getElementById('submit_form').submit();">
</form>
<?
	die();
}

?>
