<? if(strlen($error)): ?>
	<!-- ошибка -->
	<div class="error"><?= $error ?></div>
<? endif; ?>

<!-- валюты -->
<h3>Валюты</h3>
<table class="admin_table" cellspacing="0">
<? foreach($_kassa_currency as $v): ?>
	<tr>
		<td><?= $v['id'] ?></td>
		<td><input type="text" id="kassa_editcurrency<?= $v['id'] ?>_symbol" size="1" value="<?= $v['symbol'] ?>"></td>
		<td><input type="text" id="kassa_editcurrency<?= $v['id'] ?>_name" value="<?= h($v['name']) ?>"></td>
		<td><input type="button" value="Сохранить" onclick="location='?module=kassa&page=1&editcurrency=<?= $v['id'] ?>&symbol='+ge('kassa_editcurrency<?= $v['id'] ?>_symbol').value+'&name='+ge('kassa_editcurrency<?= $v['id'] ?>_name').value"></td>
		<td><input type="button" value="Удалить" onclick="location='?module=kassa&page=1&delcurrency=<?= $v['id'] ?>'"></td>
	</tr>
<? endforeach; ?>
</table>
<span class="command" onclick="ge('kassa_addcurrency_form').style.display='block';">Добавить &raquo;</span>
<div class="hidden_form" id="kassa_addcurrency_form">
	<table class="admin_table" cellspacing="0">
	<tr>
		<td>Название</td>
		<td><input type="text" id="kassa_addcurrency_form_name"></td>
	</tr>
	<tr>
		<td>Символ обозначения</td>
		<td><input type="text" id="kassa_addcurrency_form_symbol" maxlength="1"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="button" value="Ок" onclick="document.location='?module=kassa&page=1&addcurrency='+ge('kassa_addcurrency_form_name').value+'&symbol='+ge('kassa_addcurrency_form_symbol').value"></td>
	</tr>
	</table>
</div>
<!-- /валюты -->

<!-- счета -->
<h3>Счета</h3>
<table class="admin_table" cellspacing="0">
<? foreach($_kassa_account as $v): ?>
	<tr>
		<td><?= $v['id'] ?></td>
		<td><input type="text" id="kassa_editaccount<?= $v['id'] ?>" value="<?= h($v['name']) ?>"></td>
		<td><input type="button" value="Сохранить" onclick="location='?module=kassa&page=1&editaccount=<?= $v['id'] ?>&name='+ge('kassa_editaccount<?= $v['id'] ?>').value"></td>
		<td><input type="button" value="Удалить" onclick="location='?module=kassa&page=1&delaccount=<?= $v['id'] ?>'"></td>
	</tr>
<? endforeach; ?>
</table>
<span class="command" onclick="ge('kassa_addaccount_form').style.display='block';">Добавить &raquo;</span>
<div class="hidden_form" id="kassa_addaccount_form">
	<table class="admin_table" cellspacing="0">
	<tr>
		<td>Название</td>
		<td><input type="text" id="kassa_addaccount_form_name"></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td><input type="button" value="Ок" onclick="document.location='?module=kassa&page=1&addaccount='+ge('kassa_addaccount_form_name').value"></td>
	</tr>
	</table>
</div>
<!-- /счета -->

<!-- группы типов операций -->
<h3>Типы операций</h3>
<? foreach($_kassa_optype as $v): ?>
	<input type="text" id="kassa_editoptypegroup<?= $v['id'] ?>" value="<?= h($v['name']) ?>">
	<? if($v['id']): ?>
		<input type="button" value="Сохранить" onclick="location='?module=kassa&page=1&editoptypegroup=<?= $v['id'] ?>&name='+ge('kassa_editoptypegroup<?= $v['id'] ?>').value">
		<input type="button" value="Удалить" onclick="location='?module=kassa&page=1&deloptypegroup=<?= $v['id'] ?>'">
	<? endif; ?>
	<fieldset>
		<!-- типы операций -->
		<legend>Типы операций <?= $v['name'] ? 'в группе &laquo;'.$v['name'].'&raquo;' : 'без группы' ?></legend>
		<table class="admin_table" cellspacing="0">
		<? foreach($v['operation_types'] as $vv): ?>
			<tr>
				<td><?= $vv['id'] ?></td>
				<td><input type="text" id="kassa_editoptype<?= $vv['id'] ?>_name" value="<?= h($vv['name']) ?>"></td>
				<td><input type="checkbox" id="kassa_editoptype<?= $vv['id'] ?>_is_income"<?= $vv['is_income']   ? ' checked' : '' ?>> <label for="kassa_editoptype<?= $vv['id']?>_is_income">приход</label></td>
				<td><input type="checkbox" id="kassa_editoptype<?= $vv['id'] ?>_is_service"<?= $vv['is_service'] ? ' checked' : '' ?>> <label for="kassa_editoptype<?= $vv['id']?>_is_service">сервисная операция</label></td>
				<td><input type="button" value="Сохранить" onclick="location='?module=kassa&page=1&editoptype=<?= $vv['id'] ?>&name='+ge('kassa_editoptype<?= $vv['id'] ?>_name').value+'&is_income='+ge('kassa_editoptype<?= $vv['id'] ?>_is_income').checked+'&is_service='+ge('kassa_editoptype<?= $vv['id'] ?>_is_service').checked"></td>
				<td><input type="button" value="Удалить" onclick="location='?module=kassa&page=1&deloptype=<?= $vv['id'] ?>'"></td>
			</tr>
		<? endforeach; ?>
		</table>
		<span class="command" onclick="ge('kassa_addoptype<?= $v['id'] ?>_form').style.display='block'">Добавить тип операций &raquo;</span>
		<div class="hidden_form" id="kassa_addoptype<?= $v['id'] ?>_form">
			<table class="admin_table" cellspacing="0">
				<tr>
					<td>Название</td>
					<td><input type="text" id="kassa_addoptype<?= $v['id'] ?>_form_name"></td>
				</tr>
				<tr>
					<td>Тип</td>
					<td>
						<input type="radio" name="kassa_addoptype<?= $v['id'] ?>_form_income" id="kassa_addoptype<?= $v['id'] ?>_form_income" value="1"> <label for="kassa_addoptype<?= $v['id'] ?>_form_income">Приход</label><br>
						<input type="radio" name="kassa_addoptype<?= $v['id'] ?>_form_income" id="kassa_addoptype<?= $v['id'] ?>_form_income0" value="0" checked> <label for="kassa_addoptype<?= $v['id'] ?>_form_income0">Расход</label>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><input type="button" value="Ок" onclick="document.location='?module=kassa&page=1&addoptype='+ge('kassa_addoptype<?= $v['id'] ?>_form_name').value+'&group=<?= $v['id'] ?>&is_income='+ge('kassa_addoptype<?= $v['id'] ?>_form_income').checked"></td>
				</tr>
			</table>
		</div>
	</fieldset>
	<!-- /типы операций -->
<? endforeach; ?>
<span class="command" onclick="ge('kassa_addoptypegtoup_form').style.display='block'">Добавить группу типов операций &raquo;</span>
<div class="hidden_form" id="kassa_addoptypegtoup_form">
	<table class="admin_table" cellspacing="0">
		<tr>
			<td>Название</td>
			<td><input type="text" id="kassa_addoptypegtoup_form_name"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="button" value="Ок" onclick="document.location='?module=kassa&page=1&addoptypegroup='+ge('kassa_addoptypegtoup_form_name').value"></td>
		</tr>
	</table>
</div>
<!-- /группы типов операций -->