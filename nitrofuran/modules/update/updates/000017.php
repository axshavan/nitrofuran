<?php

// 000017
// добавление параметра admin_menu в static

if(!CModule::IsModuleInstalled('static'))
{
	// static не установлен
	return true;
}

return new_param
(
	'static',
	'admin_menu',
	'Пункты админского меню',
	'textarray',
	serialize
	(
		array
		(
			1 => 'Дерево страниц',
			2 => 'Список страниц'
		)
	)
);

?>