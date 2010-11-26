<? if(strlen($error)): ?>
	<!-- ошибка -->
	<div class="error"><?= $error ?></div>
<? endif; ?>

<script type="text/javascript" src="/js/kassa.js"></script>
<a href="/kassa/">&laquo; назад в кассу</a>

<h3>Запланированные операции</h3>
<table class="admin_table" cellspacing="0">
<tr>
	<th>id</th>
	<th>Название</th>
	<th>Тип операции</th>
	<th>Сумма, активность</th>
	<th>Тип расписания</th>
	<th>Расписание</th>
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
		<td>
			<input type="text" size="6" id="plan_<?= $id ?>_amount" value="<?= $_plan['amount'] ?>"><br>
			<input type="checkbox" id="plan_<?= $id ?>_active"<?= $_plan['active'] ? ' checked ' : '' ?>> <label for="plan_<?= $id ?>_active">активен</label>
		</td>
		<td>
			<select id="plan_<?= $id ?>_repeat_type" onchange="adminPlanRepeatTypeChange(this.value, '<?= $id ?>')">
				<option value="none"   <?= $_plan['repeat_type'] == 'none'    ? ' selected' : '' ?>>единоразовый расход</option>
				<option value="daily"  <?= $_plan['repeat_type'] == 'daily'   ? ' selected' : '' ?>>ежедневный расход</option>
				<option value="weekly" <?= $_plan['repeat_type'] == 'weekly'  ? ' selected' : '' ?>>еженедельный расход</option>
				<option value="monthly"<?= $_plan['repeat_type'] == 'monthly' ? ' selected' : '' ?>>ежемесячный расход</option>
			</select>
		</td>
		<td>
			<input type="hidden" id="plan_<?= $id ?>_repeat" value="<?= $_plan['repeat'] ?>">
			<div id="repeattype_<?= $id ?>_none"<?= $_plan['repeat_type'] == 'none' ? '' : ' class="invisible"' ?>>
				<span class="comment">Дата в формате YYYY-MM-DD</span><br>
				<input type="text" onkeyup="ge('kassa_addplan_form_repeat').value = this.value;" value="<?= $_plan['repeat_type'] == 'none' ? $_plan['repeat'] : '' ?>">
			</div>
			<div id="repeattype_<?= $id ?>_daily"<?= $_plan['repeat_type'] == 'daily' ? '' : ' class="invisible"' ?>><span class="comment">Особых опций не требуется</span></div>
			<div id="repeattype_<?= $id ?>_weekly"<?= $_plan['repeat_type'] == 'weekly' ? '' : ' class="invisible"' ?>>
				<span class="comment">Дни недели</span>
				<table class="admin_table" cellspacing="0">
					<tr>
						<td><label for="week_day_<?= $id ?>_1">Пн</label></td>
						<td><label for="week_day_<?= $id ?>_2">Вт</label></td>
						<td><label for="week_day_<?= $id ?>_3">Ср</label></td>
						<td><label for="week_day_<?= $id ?>_4">Чт</label></td>
						<td><label for="week_day_<?= $id ?>_5">Пт</label></td>
						<td><label for="week_day_<?= $id ?>_6">Сб</label></td>
						<td><label for="week_day_<?= $id ?>_7">Вс</label></td>
					</tr>
					<tr>
						<td><input type="checkbox" id="week_day_<?= $id ?>_1" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 1, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '1') !== false ? ' checked' : '' ?>></td>
						<td><input type="checkbox" id="week_day_<?= $id ?>_2" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 2, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '2') !== false ? ' checked' : '' ?>></td>
						<td><input type="checkbox" id="week_day_<?= $id ?>_3" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 3, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '3') !== false ? ' checked' : '' ?>></td>
						<td><input type="checkbox" id="week_day_<?= $id ?>_4" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 4, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '4') !== false ? ' checked' : '' ?>></td>
						<td><input type="checkbox" id="week_day_<?= $id ?>_5" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 5, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '5') !== false ? ' checked' : '' ?>></td>
						<td><input type="checkbox" id="week_day_<?= $id ?>_6" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 6, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '6') !== false ? ' checked' : '' ?>></td>
						<td><input type="checkbox" id="week_day_<?= $id ?>_7" onclick="adminPlanRepeatTypeC1('<?= $id ?>', 7, this.checked)"<?= $_plan['repeat_type'] == 'weekly' && strpos($_plan['repeat'], '7') !== false ? ' checked' : '' ?>></td>
					</tr>
				</table>
			</div>
			<div id="repeattype_<?= $id ?>_monthly"<?= $_plan['repeat_type'] == 'monthly' ? '' : ' class="invisible"' ?>>
				<span class="comment">Дни месяца</span>
				<table class="admin_table" cellspacing="0">
					<tr>
					<?
					$repeat = explode(',', $_plan['repeat']);
					?>
					<? for($i = 1; $i <= 31; $i++): ?>
						<?= ($i - 1) % 7 ? '' : '</tr><tr>' ?>
						<td><input type="checkbox" id="month_day_<?= $id ?>_<?= $i ?>" onclick="adminPlanRepeatTypeC2('<?= $id ?>', '<?= $i ?>', this.checked)"<?= $_plan['repeat_type'] == 'monthly' && in_array($i, $repeat) !== false ? ' checked' : '' ?>><label for="month_day_<?= $i ?>"><?= $i ?></label></td>
					<? endfor; ?>
					</tr>
				</table>
			</div>
		</td>
		<td>
			<img src="/i/admin/ok.gif" class="button" alt="Сохранить" onclick="location='?module=kassa&page=2&editplan=<?= $id ?>&name='+ge('plan_<?= $id ?>_name').value+'&optype='+ge('plan_<?= $id ?>_optype').value+'&amount='+ge('plan_<?= $id ?>_amount').value+'&type='+ge('plan_<?= $id ?>_repeat_type').value+'&repeat='+ge('plan_<?= $id ?>_repeat').value+'&active='+ge('plan_<?= $id ?>_active').checked">
			<img src="/i/admin/del.gif" class="button" alt="Удалить" onclick="location='?module=kassa&page=2&delplan=<?= $id ?>'">
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
				<select id="kassa_addplan_form_repeattype" onchange="adminPlanRepeatTypeChange2(this.value)">
					<option value="none" selected>единоразовый расход</option>
					<option value="daily">ежедневный расход</option>
					<option value="weekly">еженедельный расход</option>
					<option value="monthly">ежемесячный расход</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Расписание</td>
			<td>
				<input type="hidden" id="kassa_addplan_form_repeat">
				<div id="repeattype_none">
					<span class="comment">Дата в формате YYYY-MM-DD</span><br>
					<input type="text" onkeyup="ge('kassa_addplan_form_repeat').value = this.value;">
				</div>
				<div id="repeattype_daily"><span class="comment">Особых опций не требуется</span></div>
				<div id="repeattype_weekly">
					<span class="comment">Дни недели</span>
					<table class="admin_table" cellspacing="0">
						<tr>
							<td><label for="week_day_1">Пн</label></td>
							<td><label for="week_day_2">Вт</label></td>
							<td><label for="week_day_3">Ср</label></td>
							<td><label for="week_day_4">Чт</label></td>
							<td><label for="week_day_5">Пт</label></td>
							<td><label for="week_day_6">Сб</label></td>
							<td><label for="week_day_7">Вс</label></td>
						</tr>
						<tr>
							<td><input type="checkbox" id="week_day_1" onclick="adminPlanRepeatTypeD1(1, this.checked)"></td>
							<td><input type="checkbox" id="week_day_2" onclick="adminPlanRepeatTypeD1(2, this.checked)"></td>
							<td><input type="checkbox" id="week_day_3" onclick="adminPlanRepeatTypeD1(3, this.checked)"></td>
							<td><input type="checkbox" id="week_day_4" onclick="adminPlanRepeatTypeD1(4, this.checked)"></td>
							<td><input type="checkbox" id="week_day_5" onclick="adminPlanRepeatTypeD1(5, this.checked)"></td>
							<td><input type="checkbox" id="week_day_6" onclick="adminPlanRepeatTypeD1(6, this.checked)"></td>
							<td><input type="checkbox" id="week_day_7" onclick="adminPlanRepeatTypeD1(7, this.checked)"></td>
						</tr>
					</table>
				</div>
				<div id="repeattype_monthly">
					<span class="comment">Дни месяца</span>
					<table class="admin_table" cellspacing="0">
						<tr>
						<? for($i = 1; $i <= 31; $i++): ?>
							<?= ($i - 1) % 7 ? '' : '</tr><tr>' ?>
							<td><input type="checkbox" id="month_day_<?= $i ?>" onclick="adminPlanRepeatTypeD2('<?= $i ?>', this.checked)"><label for="month_day_<?= $i ?>"><?= $i ?></label></td>
						<? endfor; ?>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="button" value="Ok" onclick="document.location='?module=kassa&page=2&addplan&name='+ge('kassa_addplan_form_name').value+'&optype='+ge('kassa_addplan_form_optype').value+'&amount='+ge('kassa_addplan_form_amount').value+'&active='+ge('kassa_addplan_form_active').checked+'&repeat_type='+ge('kassa_addplan_form_repeattype').value+'&repeat='+ge('kassa_addplan_form_repeat').value">
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	ge('kassa_addplan_form_repeattype').value = 'none';
	adminPlanRepeatTypeChange2('none')
</script>