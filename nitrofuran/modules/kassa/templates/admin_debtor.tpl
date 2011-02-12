<a href="/kassa/">&laquo; назад в кассу</a>

<table cellspacing="0" class="admin_table">
	<? foreach($_debtors as $id => $_debtor): ?>
		<tr>
			<form action="/admin/?module=kassa&page=3" method="post" id="debtor_<?= $id ?>_form">
				<input type="hidden" name="edit" value="<?= $id ?>">
				<td><?= $id ?></td>
				<td><input type="text" id="debtor_name_<?= $id ?>" name="name" value="<?= $_debtor['name'] ?>"></td>
				<td>
					<img src="/i/admin/ok.gif" class="button" onclick="document.getElementById('debtor_<?= $id ?>_form').submit();">
					<img src="/i/admin/del.gif" class="button" onclick="if(confirm('Что, правда удалить и простить все долги?')) document.location='/admin/?module=kassa&page=3&delete=<?= $id ?>'">
				</td>
			</form>
		</tr>
	<? endforeach; ?>
	<tr>
		<form action="/admin/?module=kassa&page=3" method="post" id="debtor_add_form">
			<td>+</td>
			<td><input type="text" id="debtor_name_new" name="add" value=""></td>
			<td>
				<img src="/i/admin/add.gif" class="button" onclick="document.getElementById('debtor_add_form').submit();">
			</td>
		</form>
	</tr>
</table>