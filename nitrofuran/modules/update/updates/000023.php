<?

// 000023
// привязка холдов в кассе к валюте и счёту

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

global $DB;
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/kassa/config.php');
$DB->Query("ALTER TABLE `".KASSA_HOLD_TABLE."` ADD COLUMN `currency_id` integer UNSIGNED NOT NULL AFTER `operation_type_id`, ADD COLUMN `account_id` integer UNSIGNED NOT NULL AFTER `currency_id`");
$res  = $DB->Query("select `id` from `".KASSA_CURRENCY_TABLE."` where `default`");
$_row = $DB->Fetch($res);
if($_row['id'])
{
	$DB->Query("update `".KASSA_HOLD_TABLE."` set `currency_id` = '".$_row['id']."'");
}
$res  = $DB->Query("select `id` from `".KASSA_ACCOUNT_TABLE."` where `default`");
$_row = $DB->Fetch($res);
if($_row['id'])
{
	$DB->Query("update `".KASSA_HOLD_TABLE."` set `account_id` = '".$_row['id']."'");
}

?>