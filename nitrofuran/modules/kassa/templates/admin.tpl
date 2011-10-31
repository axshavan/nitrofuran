<? if(strlen($error)): ?>
	<!-- ошибка -->
	<div class="error"><?= $error ?></div>
<? endif; ?>

<a href="/kassa/">&laquo; назад в кассу</a>

<!-- валюты -->
<h3>Валюты</h3>
<table class="admin_table" cellspacing="0">
<? foreach($_kassa_currency as $v): ?>
	<form action="/admin/?module=kassa&page=1" method="post">
		<input type="hidden" name="editcurrency" value="<?= $v['id'] ?>">
		<tr>
			<td><?= $v['id'] ?></td>
			<td><input type="text" id="kassa_editcurrency<?= $v['id'] ?>_symbol" name="symbol" size="1" value="<?= $v['symbol'] ?>"></td>
			<td><input type="text" id="kassa_editcurrency<?= $v['id'] ?>_name" name="name" value="<?= h($v['name']) ?>"></td>
			<td><input type="checkbox" id="kassa_editcurrency<?= $v['id'] ?>_default" name="default" value="1"<?= $v['default'] ? " checked" : "" ?>> <label for="kassa_editcurrency<?= $v['id'] ?>_default">по умолчанию</label></td>
			<td><input type="submit" value="Сохранить"></td>
			<td><input type="button" value="Удалить" onclick="location='?module=kassa&page=1&delcurrency=<?= $v['id'] ?>'"></td>
		</tr>
	</form>
<? endforeach; ?>
</table>
<span class="command" onclick="ge('kassa_addcurrency_form').style.display='block';">Добавить &raquo;</span>
<div class="hidden_form" id="kassa_addcurrency_form">
	<form action="/admin/?module=kassa&page=1" method="post">
		<table class="admin_table" cellspacing="0">
		<tr>
			<td>Название</td>
			<td><input type="text" id="kassa_addcurrency_form_name" name="addcurrency"></td>
		</tr>
		<tr>
			<td>Символ обозначения</td>
			<td><input type="text" id="kassa_addcurrency_form_symbol" maxlength="1" name="symbol"></td>
		</tr>
		<tr>
			<td>Предлагать по умолчанию</td>
			<td><input type="checkbox" id="kassa_addcurrency_form_default" value="1" name="default"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Ок"></td>
		</tr>
		</table>
	</form>
</div>
<!-- /валюты -->

<!-- счета -->
<h3>Счета</h3>
<table class="admin_table" cellspacing="0">
<? foreach($_kassa_account as $v): ?>
	<form action="/admin/?module=kassa&page=1" method="post">
		<input type="hidden" name="editaccount" value="<?= $v['id'] ?>">
		<tr>
			<td><?= $v['id'] ?></td>
			<td><input type="text" id="kassa_editaccount<?= $v['id'] ?>" name="name" value="<?= h($v['name']) ?>"></td>
			<td><input type="checkbox" id="kassa_editaccount<?= $v['id'] ?>_default" name="default" value="1"<?= $v['default'] ? " checked" : "" ?>> <label for="kassa_editaccount<?= $v['id'] ?>_default">по умолчанию</label></td>
			<td><input type="submit" value="Сохранить"></td>
			<td><input type="button" value="Удалить" onclick="location='?module=kassa&page=1&delaccount=<?= $v['id'] ?>'"></td>
		</tr>
	</form>
<? endforeach; ?>
</table>
<span class="command" onclick="ge('kassa_addaccount_form').style.display='block';">Добавить &raquo;</span>
<div class="hidden_form" id="kassa_addaccount_form">
	<form action="/admin/?module=kassa&page=1" method="post">
		<table class="admin_table" cellspacing="0">
		<tr>
			<td>Название</td>
			<td><input type="text" id="kassa_addaccount_form_name" name="addaccount"></td>
		</tr>
		<tr>
			<td>Предлагать по умолчанию</td>
			<td><input type="checkbox" id="kassa_addaccount_form_default" value="1" name="default"></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Ок"></td>
		</tr>
		</table>
	</form>
</div>
<!-- /счета -->

<!-- группы типов операций -->
<h3>Типы операций</h3>
<? foreach($_kassa_optype as $v): ?>
	<form action="/admin/?module=kassa&page=1" method="post">
		<input type="text" id="kassa_editoptypegroup<?= $v['id'] ?>" name="name" value="<?= h($v['name']) ?>">
		<? if($v['id']): ?>
			<input type="hidden" name="editoptypegroup" value="<?= $v['id'] ?>">
			<input type="submit" value="Сохранить">
			<input type="button" value="Удалить" onclick="location='?module=kassa&page=1&deloptypegroup=<?= $v['id'] ?>'">
		<? endif; ?>
	</form>
	<fieldset>
		<!-- типы операций -->
		<legend>Типы операций <?= $v['name'] ? 'в группе &laquo;'.$v['name'].'&raquo;' : 'без группы' ?></legend>
		<table class="admin_table" cellspacing="0">
		<? foreach($v['operation_types'] as $vv): ?>
			<form action="/admin/?module=kassa&page=1" method="post">
				<input type="hidden" name="editoptype" value="<?= $vv['id'] ?>">
				<tr>
					<td><?= $vv['id'] ?></td>
					<td><input type="text" id="kassa_editoptype<?= $vv['id'] ?>_name" name="name" value="<?= h($vv['name']) ?>"></td>
					<td><input type="checkbox" id="kassa_editoptype<?= $vv['id'] ?>_is_income" name="is_income"<?= $vv['is_income']   ? ' checked' : '' ?>> <label for="kassa_editoptype<?= $vv['id']?>_is_income">приход</label></td>
					<td><input type="checkbox" id="kassa_editoptype<?= $vv['id'] ?>_is_service" name="is_service"<?= $vv['is_service'] ? ' checked' : '' ?>> <label for="kassa_editoptype<?= $vv['id']?>_is_service">сервисная операция</label></td>
					<td><input type="submit" value="Сохранить"></td>
					<td><input type="button" value="Удалить" onclick="location='?module=kassa&page=1&deloptype=<?= $vv['id'] ?>'"></td>
				</tr>
			</form>
		<? endforeach; ?>
		</table>
		<span class="command" onclick="ge('kassa_addoptype<?= $v['id'] ?>_form').style.display='block'">Добавить тип операций &raquo;</span>
		<div class="hidden_form" id="kassa_addoptype<?= $v['id'] ?>_form">
			<form action="/admin/?module=kassa&page=1" method="post">
				<input type="hidden" name="group" value="<?= $v['id'] ?>">
				<table class="admin_table" cellspacing="0">
					<tr>
						<td>Название</td>
						<td><input type="text" id="kassa_addoptype<?= $v['id'] ?>_form_name" name="addoptype"></td>
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
						<td><input type="submit" value="Оk"></td>
					</tr>
				</table>
			</form>
		</div>
	</fieldset>
	<!-- /типы операций -->
<? endforeach; ?>
<span class="command" onclick="ge('kassa_addoptypegtoup_form').style.display='block'">Добавить группу типов операций &raquo;</span>
<div class="hidden_form" id="kassa_addoptypegtoup_form">
	<form action="/admin/?module=kassa&page=1" method="post">
		<table class="admin_table" cellspacing="0">
			<tr>
				<td>Название</td>
				<td><input type="text" id="kassa_addoptypegtoup_form_name" name="addoptypegroup"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Ок"></td>
			</tr>
		</table>
	</form>
</div>
<!-- /группы типов операций -->