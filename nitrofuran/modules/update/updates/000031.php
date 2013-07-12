<?php

// 000031
// добавление ещё одного чекбокса в кассу

if(!CModule::IsModuleInstalled('kassa'))
{
	// касса не установлена
	return true;
}

new_param('kassa', 'use_blue_template', 'Синяя касса', 'checkbox');

?>