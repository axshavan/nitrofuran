<?php

// 000042
// добавить возможность включения асинхронного обновления в ридер

if(!CModule::IsModuleInstalled('reader'))
{
	// ридер не установлен
	return true;
}

new_param('reader', 'use_async_run', 'Асинхронное обновление подписок', 'checkbox');

?>