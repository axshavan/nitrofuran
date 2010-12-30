<?php

// 000004
// установка модуля kokol (если он ещё не установлен)

global $DB;
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(!isset($r['kokol']))
{
	require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/kokol.php');
}

?>