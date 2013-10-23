<?php

// 000043
// локальные апдейты

new_param('update', 'local_version', 'Локальная версия таблиц', 'text', 0);
new_param('update', 'local_last_update', 'Дата последнего обновления локальной версии', 'text', 0);
$_params = unserialize(get_param('update', 'admin_menu'));
$_params[3] = "Локальное обновления";
set_param('update', 'admin_menu', serialize($_params));

?>