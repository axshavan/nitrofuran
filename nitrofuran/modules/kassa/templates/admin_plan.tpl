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
		<form action="?module=kassa&page=2" method="post" id="plan_<?= $_plan['id'] ?>_form">
			<td><?= $id ?><input type="hidden" name="editplan" value="<?= $_plan['id'] ?>"></td>
			<td><input type="text" name="name" value="<?= h($_plan['name']) ?>"></td>
			<td>
				<select name="optype">
					<? foreach($_kassa_optype_byid as $_optype): ?>
						<option value="<?= $_optype['id'] ?>"<?= $_optype['id'] == $_plan['operation_type_id'] ? ' selected' : '' ?>><?= h($_optype['name']) ?></option>
					<? endforeach; ?>
				</select>
			</td>
			<td>
				<input type="text" size="6" name="amount" value="<?= $_plan['amount'] ?>">
				<select name="currency">
					<? foreach($_kassa_currency as $c): ?>
						<option value="<?= $c['id'] ?>"<?= $_plan['currency_id'] == $c['id'] ? ' selected' : ''?>><?= $c['symbol'] ?></option>
					<? endforeach; ?>
				</select>
				<input type="checkbox" id="plan_<?= $_plan['id'] ?>_active" name="active"<?= $_plan['active'] ? ' checked ' : '' ?>> <label for="plan_<?= $id ?>_active">активен</label>
			</td>
			<td>
				<select name="type" onchange="adminPlanRepeatTypeChange(this.value, '<?= $id ?>')">
					<option value="none"   <?= $_plan['repeat_type'] == 'none'    ? ' selected' : '' ?>>единоразовая операция</option>
					<option value="daily"  <?= $_plan['repeat_type'] == 'daily'   ? ' selected' : '' ?>>ежедневная операция</option>
					<option value="weekly" <?= $_plan['repeat_type'] == 'weekly'  ? ' selected' : '' ?>>еженедельная операция</option>
					<option value="monthly"<?= $_plan['repeat_type'] == 'monthly' ? ' selected' : '' ?>>ежемесячная операция</option>
				</select>
			</td>
			<td>
				<input type="hidden" name="repeat" id="plan_<?= $id ?>_repeat" value="<?= $_plan['repeat'] ?>">
				<div id="repeattype_<?= $id ?>_none"<?= $_plan['repeat_type'] == 'none' ? '' : ' class="invisible"' ?>>
					<span class="comment">Дата в формате YYYY-MM-DD</span><br>
					<input type="text" onkeyup="ge('plan_<?= $id ?>_repeat').value = this.value;" value="<?= $_plan['repeat_type'] == 'none' ? $_plan['repeat'] : '' ?>">
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
							<td class="nowrap"><input type="checkbox" id="month_day_<?= $id ?>_<?= $i ?>" onclick="adminPlanRepeatTypeC2('<?= $id ?>', '<?= $i ?>', this.checked)"<?= $_plan['repeat_type'] == 'monthly' && in_array($i, $repeat) !== false ? ' checked' : '' ?>><label for="month_day_<?= $i ?>"><?= $i ?></label></td>
						<? endfor; ?>
						</tr>
					</table>
				</div>
			</td>
			<td>
				<img src="/i/admin/ok.gif" class="button" alt="Сохранить" onclick="ge('plan_<?= $_plan['id'] ?>_form').submit();">
				<img src="/i/admin/del.gif" class="button" alt="Удалить" onclick="location='?module=kassa&page=2&delplan=<?= $id ?>'">
			</td>
		</form>
	</tr>
<? endforeach; ?>
</table>

<span class="command" onclick="ge('kassa_addplan_form_div').style.display='block';">Добавить &raquo;</span>
<div class="hidden_form" id="kassa_addplan_form_div">
	<form action="?module=kassa&page=2" method="post" id="kassa_addplan_form">
		<input type="hidden" name="addplan" value="1">
		<table class="admin_table" cellspacing="0">
			<tr>
				<td><label for="kassa_addplan_form_name">Название</label></td>
				<td><input type="text" id="kassa_addplan_form_name" name="name"></td>
			</tr>
			<tr>
				<td><label for="kassa_addplan_form_optype">Тип операции</label></td>
				<td>
					<select id="kassa_addplan_form_optype" name="optype">
						<? foreach($_kassa_optype_byid as $_optype): ?>
							<option value="<?= $_optype['id'] ?>"><?= $_optype['name'] ?></option>
						<? endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="kassa_addplan_form_amount">Сумма</label></td>
				<td>
					<input type="text" id="kassa_addplan_form_amount" name="amount">
				</td>
			</tr>
			<tr>
				<td><label for="kassa_addplan_form_currency">Валюта</label></td>
				<td>
					<select id="kassa_addplan_form_currency" name="currency">
						<? foreach($_kassa_currency as $c): ?>
							<option value="<?= $c['id'] ?>"><?= $c['symbol'] ?></option>
						<? endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="kassa_addplan_form_active">Активность</label></td>
				<td><input type="checkbox" id="kassa_addplan_form_active" name="active" checked></td>
			</tr>
			<tr>
				<td><label for="kassa_addplan_form_repeattype">Тип расписания</label></td>
				<td>
					<select id="kassa_addplan_form_repeattype" onchange="adminPlanRepeatTypeChange2(this.value)" name="repeat_type">
						<option value="none" selected>единоразовая операция</option>
						<option value="daily">ежедневная операция</option>
						<option value="weekly">еженедельная операция</option>
						<option value="monthly">ежемесячная операция</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for="kassa_addplan_form_repeat">Расписание</label></td>
				<td>
					<input type="hidden" id="kassa_addplan_form_repeat" name="repeat">
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
					<input type="button" value="Ok" onclick="ge('kassa_addplan_form').submit();">
				</td>
			</tr>
		</table>
	</form>
</div>
<script type="text/javascript">
	ge('kassa_addplan_form_repeattype').value = 'none';
	adminPlanRepeatTypeChange2('none')
</script>