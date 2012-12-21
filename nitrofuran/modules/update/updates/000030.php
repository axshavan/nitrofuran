<?php

// 000030
// добавление страницы в модуль обновлений

$_params = unserialize(get_param('update', 'admin_menu'));
$_params[2] = "Скачать с github.com";
set_param('update', 'admin_menu', serialize($_params));

?>