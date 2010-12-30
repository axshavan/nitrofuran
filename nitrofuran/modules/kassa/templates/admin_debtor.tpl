<a href="/kassa/">&laquo; назад в кассу</a>

<table cellspacing="0" class="admin_table">
	<? foreach($_debtors as $id => $_debtor): ?>
		<tr>
			<td><?= $id ?></td>
			<td><input type="text" id="debtor_name_<?= $id ?>" name="debtor_name_<?= $id ?>" value="<?= $_debtor['name'] ?>"></td>
			<td>
				<img src="/i/admin/ok.gif" class="button" onclick="document.location='/admin/?module=kassa&page=3&edit=<?= $id ?>&name='+ge('debtor_name_<?= $id ?>').value">
				<img src="/i/admin/del.gif" class="button" onclick="if(confirm('Что, правда удалить и простить все долги?')) document.location='/admin/?module=kassa&page=3&delete=<?= $id ?>'">
			</td>
		</tr>
	<? endforeach; ?>
	<tr>
		<td>+</td>
		<td><input type="text" id="debtor_name_new" name="debtor_name_new" value=""></td>
		<td>
			<img src="/i/admin/add.gif" class="button" onclick="document.location='/admin/?module=kassa&page=3&add='+ge('debtor_name_new').value">
		</td>
	</tr>
</table>