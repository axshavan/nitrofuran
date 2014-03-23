<a href="?page=blogadd">Добавить новый блог</a>

<? if(sizeof($_blogs)): ?>
	<table class="admin_table">
		<tr>
			<th>Название блога</th>
			<th>Владелец</th>
			<th>Путь к блогу</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach($_blogs as $blog): ?>
			<tr>
				<td><?= h($blog['name']) ?></td>
                <td><?= $blog['user_id'] ?></td>
                <td><?= $blog['tree_id'] ?></td>
                <td></td>
			</tr>
		<? endforeach; ?>
	</table>
<? else: ?>
	<div class="error">Нет блогов</div>
<? endif;?>