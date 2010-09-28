<? if(strlen($error)): ?>
	<!-- ошибка -->
	<div class="error"><?= $error ?></div>
<? endif; ?>

<h3>Запланированные операции</h3>
<table class="admin_table" cellspacing="0">
<tr>
	<th>id</th>
	<th>Название</th>
	<th>Тип операции</th>
	<th>Сумма</th>
	<th>Активность</th>
	<th>Тип расписания</th>
	<th>Расписание</th>
	<th></th>
</tr>
<? foreach($_plans as $id => $_plan): ?>
	<tr>
		<td><?= $id ?></td>
		<td><input type="text" id="plan_<?= $id ?>_name" value="<?= $_plan['name'] ?>"></td>
		<td>
			<select id="plan_<?= $id ?>_optype">
				<? foreach($_kassa_optype_byid as $_optype): ?>
					<option value="<?= $_optype['id'] ?>"<?= $_optype['id'] == $_plan['operation_type_id'] ? ' selected' : '' ?>><?= $_optype['name'] ?></option>
				<? endforeach; ?>
			</select>
		</td>
		<td><input type="text" size="4" id="plan_<?= $id ?>_amount" value="<?= $_plan['amount'] ?>"></td>
		<td><input type="checkbox" id="plan_<?= $id ?>_active"<?= $_plan['active'] ? ' checked ' : '' ?>> <label for="plan<?= $id ?>">активен</label></td>
		<td>
			<select id="plan_<?= $id ?>_repeat_type">
				<option value="none"   <?= $_plan['repeat_type'] == 'none'    ? ' selected' : '' ?>>единоразовый расход</option>
				<option value="daily"  <?= $_plan['repeat_type'] == 'daily'   ? ' selected' : '' ?>>ежедневный расход</option>
				<option value="weekly" <?= $_plan['repeat_type'] == 'weekly'  ? ' selected' : '' ?>>еженедельный расход</option>
				<option value="monthly"<?= $_plan['repeat_type'] == 'monthly' ? ' selected' : '' ?>>ежемесячный расход</option>
			</select>
		</td>
		<td>
			<input type="text" id="plan_<?= $id ?>_repeat" value="<?= $_plan['repeat'] ?>">
		</td>
		<td>
			<input type="button" value="Сохранить" onclick="location='?module=kassa&page=2&editplan=<?= $id ?>&name='+ge('plan_<?= $id ?>_name').value+'&optype='+ge('plan_<?= $id ?>_optype').value+'&amount='+ge('plan_<?= $id ?>_amount').value+'&type='+ge('plan_<?= $id ?>_repeat_type').value+'&repeat='+ge('plan_<?= $id ?>_repeat').value+'&active='+ge('plan_<?= $id ?>_active').checked">
		</td>
		<td>
			<input type="button" value="Удалить" onclick="location='?module=kassa&page=2&delplan=<?= $id ?>'">
		</td>
	</tr>
<? endforeach; ?>
</table>