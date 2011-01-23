<table cellspacing="0" class="admin_table">
	<tr>
		<th colspan="3">&nbsp;</th>
		<th>Название</th>
		<th>Приход</th>
		<th>Сервисная операция</th>
		<? foreach($_kassa_properties as $_prop): ?>
			<th><?= $_prop['name'] ?> <img src="/i/admin/del.gif" onclick="if(confirm('Правда удалить свойство?')){document.location='/admin/?module=kassa&page=4&delproperty=<?= $_prop['id'] ?>';}"></th>
		<? endforeach;?>
	</tr>
	<? foreach($_kassa_optype_byid as $_optype): ?>
		<form action="/admin/?module=kassa&page=4" id="optypeform<?= $_optype['id'] ?>" method="post">
			<input type="hidden" name="optype_id" value="<?= $_optype['id'] ?>">
			<tr>
				<td><?= $_optype['id'] ?></td>
				<td><img src="/i/admin/del.gif" onclick="if(confirm('Что, правда удалить?')){document.location='/admin/?module=kassa&page=4&deloptype=<?= $_optype['id'] ?>'}"></td>
				<td><img src="/i/admin/ok.gif" onclick="ge('optypeform<?= $_optype['id'] ?>').submit();"></td>
				<td><input type="text" name="name" id="name<?= $_optype['id'] ?>" value="<?= htmlspecialchars($_optype['name']) ?>"></td>
				<td><input type="checkbox" name="is_income"  value="1" id="is_income<?=  $_optype['id'] ?>"<?= $_optype['is_income']  ? ' checked' : '' ?>></td>
				<td><input type="checkbox" name="is_service" value="1" id="is_service<?= $_optype['id'] ?>"<?= $_optype['is_service'] ? ' checked' : '' ?>></td>
			<? foreach($_kassa_properties as $_prop): ?>
				<td>
					<input
						type="<?= $_prop['type'] ?>"
						id="prop_<?= $_optype['id']?>_<?= $_prop['id']?>"
						name="prop_<?= $_prop['id']?>"
						<? if($_prop['type'] == 'text'): ?>
							value="<?= htmlspecialchars($_kassa_propvalues[$_optype['id']][$_prop['id']]) ?>"
						<? else: ?>
							value="1"
							<? if($_kassa_propvalues[$_optype['id']][$_prop['id']]): ?>
								checked
							<? endif; ?>
						<? endif; ?>
						>
				</td>
			<? endforeach; ?>
			</tr>
		</form>
	<? endforeach; ?>
</table>

<span class="command" onclick="ge('addpropertydiv').style.display='block';">Добавить свойство</span>
<div class="hidden_form" id="addpropertydiv">
	<form action="/admin/?module=kassa&page=4" method="post">
		<table class="admin_table" cellspacing="0">
			<tr>
				<td>Название</td>
				<td><input type="text" name="newpropname"></td>
			</tr>
			<tr>
				<td>Тип операции</td>
				<td>
					<input type="radio" value="text"     id="newproptype1" name="newproptype"> <label for="newproptype1">Текст</label><br>
					<input type="radio" value="checkbox" id="newproptype2" name="newproptype"> <label for="newproptype2">Чекбокс</label>
				</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Добавить"></td>
			</tr>
		</table>
	</form>
</div>