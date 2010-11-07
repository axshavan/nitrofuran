<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? elseif($success_text): ?>
	<div class="success"><?= $success_text ?></div>
<? endif; ?>

<table class="admin_table" cellspacing="0">
	<tr>
		<td>Установленная версия таблиц</td>
		<td><?= $version ?></td>
	</tr>
	<tr>
		<td>Дата последнего обновления</td>
		<td><?= $last_update ?></td>
	</tr>
	<tr>
		<td>Доступная версия таблиц</td>
		<td><?= $available_version ?></td>
	</tr>
</table>
<? if($version < $available_version): ?>
	<input type="button" value="Обновить" onclick="document.location='/admin/?module=update&page=1&proceed'">
<? endif; ?>