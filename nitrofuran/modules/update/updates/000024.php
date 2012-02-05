<?php

// 000024
// установка модуля drunkeeper

global $DB;
$r = get_param('admin', 'modules_installed');
$r = unserialize($r);
if(!isset($r['drunkeeper']))
{
	require(DOCUMENT_ROOT.'/nitrofuran/modules/install/modules/drunkeeper.php');
}

?>