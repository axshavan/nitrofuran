<?php

// 000016
// установка модуля auth

$_modules_installed = unserialize(get_param('admin', 'modules_installed'));
$_modules_installed['auth'] = 'Авторизация';
set_param('admin', 'modules_installed', serialize($_modules_installed));
return true;

?>