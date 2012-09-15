<span class="command" onclick="ge('add_static_page_form').style.display='block';">Добавить статичную страницу &raquo;</span>
<div class="hidden_form" id="add_static_page_form">
	<form action="/admin/?module=static&page=2" method="post">
		<table class="admin_table" cellspacing="0">
		<tr>
			<td>Папка, к которой привязана страница</td>
			<td>
				<select name="add2tree_id">
					<? foreach($_folders as $f): ?>
						<option value="<?= $f['id'] ?>"><?= '('.$f['id'].') '.$f['name'] ?></option>
					<? endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Ок"></td>
		</tr>
		</table>
	</form>
</div>

<table class="admin_table" cellspacing="0">
	<? foreach($_pages as $_page): ?>
		<tr>
			<td><?= $_page['id'] ?></td>
			<td><?= $_page['tree_id'] ?></td>
			<td><a href="?module=static&page=3&pageid=<?= $_page['id'] ?>">Редактировать</a></td>
			<td><?= htmlspecialchars(substr($_page['content'], 0, 100)).(strlen($_page['content']) > 100 ? '...' : '') ?></td>
		</tr>
	<? endforeach; ?>
</table>
