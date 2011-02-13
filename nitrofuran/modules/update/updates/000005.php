<?php

// 000005
// записи в кассе задним числом

require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
global $DB;
$DB->Query("alter table `".KASSA_OPERATION_TABLE."` add column `backtime` integer unsigned not null");
$DB->Query("alter table `".KASSA_OPERATION_TABLE."` add index `by_backtime` (`backtime`)");
$DB->Query("update `".KASSA_OPERATION_TABLE."` set `backtime` = `time`");

?>