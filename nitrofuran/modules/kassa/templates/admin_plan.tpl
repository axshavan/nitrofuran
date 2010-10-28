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
	<th></th>
</tr>
<? foreach($_plans as $id => $_plan): ?>
	<tr>
		<td><?= $id ?></td>
		<td><input type="text" id="plan_<?= $id ?>_name" value="<?= h($_plan['name']) ?>"></td>
		<td>
			<select id="plan_<?= $id ?>_optype">
				<? foreach($_kassa_optype_byid as $_optype): ?>
					<option value="<?= $_optype['id'] ?>"<?= $_optype['id'] == $_plan['operation_type_id'] ? ' selected' : '' ?>><?= h($_optype['name']) ?></option>
				<? endforeach; ?>
			</select>
		</td>
		<td><input type="text" size="6" id="plan_<?= $id ?>_amount" value="<?= $_plan['amount'] ?>"></td>
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
			<input type="text" id="plan_<?= $id ?>_repeat" size="8" value="<?= $_plan['repeat'] ?>">
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

<span class="command" onclick="ge('kassa_addplan_form').style.display='block';">Добавить &raquo;</span>
<div class="hidden_form" id="kassa_addplan_form">
	<table class="admin_table" cellspacing="0">
		<tr>
			<td>Название</td>
			<td><input type="text" id="kassa_addplan_form_name"></td>
		</tr>
		<tr>
			<td>Тип операции</td>
			<td>
				<select id="kassa_addplan_form_optype">
					<? foreach($_kassa_optype_byid as $_optype): ?>
						<option value="<?= $_optype['id'] ?>"><?= $_optype['name'] ?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>Сумма</td>
			<td><input type="text" id="kassa_addplan_form_amount"></td>
		</tr>
		<tr>
			<td>Активность</td>
			<td><input type="checkbox" id="kassa_addplan_form_active"></td>
		</tr>
		<tr>
			<td>Тип расписания</td>
			<td>
				<select id="kassa_addplan_form_repeattype">
					<option value="none">единоразовый расход</option>
					<option value="daily">ежедневный расход</option>
					<option value="weekly">еженедельный расход</option>
					<option value="monthly">ежемесячный расход</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Расписание</td>
			<td><input type="text" id="kassa_addplan_form_repeat"></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="button" value="Ok" onclick="document.location='?module=kassa&page=2&addplan&name='+ge('kassa_addplan_form_name').value+'&optype='+ge('kassa_addplan_form_optype').value+'&amount='+ge('kassa_addplan_form_amount').value+'&active='+ge('kassa_addplan_form_active').checked+'&repeat_type='+ge('kassa_addplan_form_repeattype').value+'&repeat='+ge('kassa_addplan_form_repeat').value">
			</td>
		</tr>
	</table>
</div>