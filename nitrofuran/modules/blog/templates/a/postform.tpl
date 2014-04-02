<?
if($error)
{
	echo '<div class="error">Произошла ошибка. Изменения не сохранены</div>';
}
?>
<form action="?page=<?= $actionpage ?>" method="post">
    <h3><?= $id ? 'Редактировать пост' : 'Добавить новый пост' ?></h3>
    <input type="hidden" name="id" value="<?= (int)$post['id'] ?>"/>
    <table class="admin_table">
		<tr>
			<td><label for="title">Заголовок</label></td>
			<td><input type="text" maxlength="255" name="title" id="title" value="<?= h($post['title']) ?>"/></td>
		</tr>
		<tr>
			<td><label for="text">Текст</label></td>
			<td><textarea rows="25" cols="80" id="text" name="text"><?= h($post['text'])  ?></textarea></td>
		</tr>
	    <tr>
		    <td><label for="blog_id">Блог</label></td>
		    <td>
			    <select name="blog_id" id="blog_id">
				    <option value="0">-</option>
				    <? foreach($_blogs as $blog): ?>
				        <option value="<?= (int)$blog['id'] ?>" <?= $blog['id'] == $post['blog_id'] ? 'selected="selected"' : '' ?>><?= h($blog['name']) ?></option>
					<? endforeach; ?>
			    </select>
		    </td>
	    </tr>
        <tr>
            <td><label for="date_create">Дата создания</label></td>
            <td><input type="text" maxlength="19" name="date_create" id="date_create" value="<?= h($post['date_create']) ?>"/></td>
        </tr>
	    <tr>
		    <td>&nbsp;</td>
		    <td><input type="submit" value="Сохранить" /></td>
	    </tr>
	</table>
</form>