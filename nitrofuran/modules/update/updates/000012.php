<?php

// 000012
// привязка операций по должнику к операциям по кассе

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');

$DB->Query("alter table `".KASSA_DEBTORS_OPERATION_TABLE."` add column `operation_id` integer default null");

?>