<? if(strlen($error)): ?>
	<!-- ошибка -->
	<div class="error"><?= $error ?></div>
<? endif; ?>

<!-- категории напитков (drink_types) -->
<h3>Категории напитков</h3>
<? foreach($_drink_types as $v): ?>
	<form action="/admin/?module=drunkeeper&page=1" method="post">
		<input type="text" id="drunkeeper_drinktype<?= $v['id'] ?>" name="name" value="<?= h($v['name']) ?>">
		<? if($v['id']): ?>
			<input type="hidden" name="editdrinktype" value="<?= $v['id'] ?>">
			<input type="submit" value="Сохранить">
			<input type="button" value="Удалить" onclick="location='?module=drunkeeper&page=1&deldrinktype=<?= $v['id'] ?>'">
		<? endif; ?>
	</form>
	<fieldset>
		<!-- собственно напитки -->
		<legend>Напитки <?= $v['name'] ? 'в группе &laquo;'.$v['name'].'&raquo;' : 'без группы' ?></legend>
		<table class="admin_table" cellspacing="0">
			<tr>
				<th>id</th>
				<th>Название</th>
				<th>Крепость, %</th>
				<th colspan="2">&nbsp;</th>
			</tr>
		<? foreach($v['drinks'] as $vv): ?>
			<form action="/admin/?module=drunkeeper&page=1" method="post">
				<input type="hidden" name="editdrink" value="<?= $vv['id'] ?>">
				<tr>
					<td><?= $vv['id'] ?></td>
					<td><input type="text" id="drunkeeper_drink<?= $vv['id'] ?>_name" name="name" value="<?= h($vv['name']) ?>"></td>
					<td><input type="text" id="drunkeeper_drink<?= $vv['id'] ?>_strength" name="strength" value="<?= h($vv['strength']) ?>"></td>
					<td><input type="submit" value="Сохранить"></td>
					<td><input type="button" value="Удалить" onclick="location='?module=drunkeeper&page=1&deldrink=<?= $vv['id'] ?>'"></td>
				</tr>
			</form>
		<? endforeach; ?>
		</table>
		<span class="command" onclick="ge('drunkeeper_addrink<?= $v['id'] ?>_form').style.display='block'">Добавить напиток &raquo;</span>
		<div class="hidden_form" id="drunkeeper_addrink<?= $v['id'] ?>_form">
			<form action="/admin/?module=drunkeeper&page=1" method="post">
				<input type="hidden" name="group" value="<?= $v['id'] ?>">
				<table class="admin_table" cellspacing="0">
					<tr>
						<td>Название</td>
						<td><input type="text" id="drunkeeper_addrink<?= $v['id'] ?>_form_name" name="adddrinkname"></td>
					</tr>
					<tr>
						<td>Крепость, %</td>
						<td>
							<input type="text" id="drunkeeper_addrink<?= $v['id'] ?>_form_strength" name="adddrinkstrength"><br>
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
	<!-- /собственно напитки -->
<? endforeach; ?>
<span class="command" onclick="ge('drunkeeper_adddrinktype_form').style.display='block'">Добавить тип напитка &raquo;</span>
<div class="hidden_form" id="drunkeeper_adddrinktype_form">
	<form action="/admin/?module=drunkeeper&page=1" method="post">
		<table class="admin_table" cellspacing="0">
			<tr>
				<td>Название</td>
				<td><input type="text" id="adddrinktypename" name="adddrinktypename"></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td><input type="submit" value="Ок"></td>
			</tr>
		</table>
	</form>
</div>
<!-- /категории напитков (drink_types) -->