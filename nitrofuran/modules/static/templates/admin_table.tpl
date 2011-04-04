<table class="admin_table" cellspacing="0">
	<? foreach($_pages as $_page): ?>
		<tr>
			<td><?= $_page['id'] ?></td>
			<td><?= $_page['tree_id'] ?></td>
			<td><a href="?module=static&page=3&pageid=<?= $_page['id'] ?>"><!--Редактировать содержимое--></a></td>
			<td><?= htmlspecialchars(substr($_page['content'], 0, 100)).(strlen($_page['content']) > 100 ? '...' : '') ?></td>
		</tr>
	<? endforeach; ?>
</table>