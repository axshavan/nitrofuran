<a href="?page=blogpostadd"><img src="/i/admin/add.gif"/> Добавить новый пост в блог</a>

<table class="admin_table">
	<tr>
		<th>id</th>
		<th>Название</th>
		<th>Длина текста</th>
		<th>Дата публикации</th>
		<th>Действия</th>
	</tr>
	<? foreach($_posts as $post): ?>
		<tr>
			<td><?= (int)$post['id'] ?></td>
			<td><?= h($post['title']) ?></td>
			<td><?= mb_strlen($post['text'], 'utf-8') ?></td>
			<td><?= date('Y-m-d H:i:s', $post['date_create']) ?></td>
			<td>
				<a href="?page=blogpostedit&id=<?= (int)$post['id'] ?>"><img src="/i/admin/edit.gif" /></a>
                <a href="?page=blogpostdelete&id=<?= (int)$post['id'] ?>"><img src="/i/admin/del.gif" /></a>
			</td>
		</tr>
	<? endforeach; ?>
</table>

Тут ещё не хватает пейджинации
<? trace($count_posts) ?>