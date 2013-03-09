<link rel="stylesheet" type="text/css" href="/css/static.css">
<script type="text/javascript" src="/js/admin.js"></script>
<script type="text/javascript" src="/js/static.js"></script>

<form action="/admin?module=static&page=3" method="post">
	<input type="hidden" name="pageid" value="<?= $_page['page']['id'] ?>">
	<input type="submit" value="Готово"onfocus="onSubmitFocus()"><br>
	<textarea onkeypress="onTextareaKeyPress(this, event.keyCode);" name="content" id="content" class="full"><?= htmlspecialchars($_page['page']['content']) ?></textarea><br>
	<? if(sizeof($_page['meta'])): ?>
		Редактировать meta-данные для страницы:<br />
		<table>
			<tr>
				<td>Ключ</td>
                <td>Значение</td>
			</tr>
			<? foreach($_page['meta'] as $id => $meta): ?>
				<tr>
					<td><input type="text" name="meta_key[<?= $id ?>]" id="meta_key_<?= $id ?>" value="<?= htmlspecialchars($meta['key']) ?>" /></td>
					<td><input type="text" name="meta_val[<?= $id ?>]" id="meta_val_<?= $id ?>" value="<?= htmlspecialchars($meta['value']) ?>" /></td>
				</tr>
			<? endforeach; ?>
        </table>
		<br />
	<? endif; ?>
	Добавить meta-данные для страницы:<br/>
	<label for="meta_new_key">Название meta-строки</label> <input type="text" name="meta_new_key" id="meta_new_key" /><br />
    <label for="meta_new_val">Значение meta-строки</label> <input type="text" name="meta_new_val" id="meta_new_val" /><br />
	<input type="submit" value="Готово" onfocus="onSubmitFocus()">
</form>