<?
/**
 * Отрисовать рекурсивно список папок
 * @param array  $item        массив с деревом папок
 * @param int    $selected_id выбранный id ветки
 * @param string $path        служебная переменная - текущий путь при рекурсивной отрисовке
 */
function draw_vtree(&$item, $selected_id = 0, $path = '/')
{
	echo '<input type="radio" name="tree_id" value="'.$item['id'].'" id="vtree'.$item['id'].'" '.($selected_id == $item['id'] ? 'checked="checked"' : '').'>'
		.'<label for="vtree'.$item['id'].'">'.($item['id'] ? $path.$item['data']['name'] : '<i>не указано</i>').'</label><br />';
	foreach($item['children'] as $subitem)
	{
		draw_vtree($subitem, $selected_id, $item['data']['name'] ? $path.$item['data']['name'].'/' : $path);
	}

}

if($error)
{
	echo '<div class="error">Произошла ошибка. Изменения не сохранены</div>';
}
?>
<form action="?page=<?= $actionpage ?>" method="post">
	<h3><?= $id ? 'Редактировать блог' : 'Добавить новый блог' ?></h3>
	<input type="hidden" name="id" value="<?= (int) $id ?>"/>
	<table class="admin_table">
		<tr>
			<td>Название блога</td>
			<td>
				<input type="text" name="name" maxlength="128" value="<?= $name ?>" />
			</td>
		</tr>
	    <tr>
	        <td>Владелец</td>
	        <td>
				<select name="user_id">
					<? foreach($userlist as $u): ?>
						<option value="<?= $u['id'] ?>" <?= $u['id'] == $userselected ? 'selected="selected"' : '' ?>><?= h($u['login'].' '.($u['full_name'] ? '('.$u['full_name'].')': '')) ?></option>
					<? endforeach; ?>
				</select>
	        </td>
	    </tr>
	    <tr>
	        <td>Привязка к папке</td>
	        <td>
				Привязать блог можно к любой ветке, но работать он будет там только в том случае, если для этой ветки установлен
		        модуль blog и действие index (или действие не задано). <a href="/admin/?module=vtree&page=1">Админка виртуального дерева папок</a><br/>
				<? draw_vtree($vtree, $vtreeselected) ?>
	        </td>
	    </tr>
		<tr>
			<td></td>
			<td><input type="submit" value="Сохранить"></td>
		</tr>
	</table>
</form>