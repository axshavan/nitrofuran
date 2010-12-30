<?
if(!function_exists('draw_vtree_item'))
{
	// функция для рекурсивной отрисовки дерева
	function draw_vtree_item(&$item, $_modules_installed)
	{
		?>
		<tr>
			<td><?= $item['id'] ?></td>
			<td>
				<input type="text" name="<?= $item['id'] ?>_name" id="<?= $item['id'] ?>_name" value="<?= htmlspecialchars($item['data']['name']) ?>">
			</td>
			<td>
				<select name="<?= $item['id'] ?>_module" id="<?= $item['id'] ?>_module">
				<option value=""<?= $item['data']['module'] ? '' : ' selected' ?>></option>
				<? foreach($_modules_installed as $module => $name): ?>
					<option value="<?= $module ?>"<?= $item['data']['module'] == $module ? ' selected' : '' ?>><?= $name ?></option>
				<? endforeach; ?>
			</select>
			</td>
			<td>
				<input type="text" name="<?= $item['id'] ?>_action" id="<?= $item['id'] ?>_action" value="<?= htmlspecialchars($item['data']['action']) ?>">
			</td>
			<td>
				<input type="text" name="<?= $item['id'] ?>_template" id="<?= $item['id'] ?>_template" value="<?= htmlspecialchars($item['data']['template']) ?>">
			</td>
			<td>
				<input type="checkbox" name="<?= $item['id'] ?>_access" id="<?= $item['id'] ?>_access" value="1"<?= $item['data']['access'] ? ' checked' : '' ?>>
			</td>
			<td>
				<img src="/i/admin/ok.gif" class="button" alt="Сохранить" onclick="document.location='/admin/?module=vtree&page=1&saveid=<?= $item['id'] ?>&n='+ge('<?= $item['id']?>_name').value+'&m='+ge('<?= $item['id']?>_module').value+'&a='+ge('<?= $item['id']?>_action').value+'&t='+ge('<?= $item['id']?>_template').value+'&s='+ge('<?= $item['id']?>_access').checked">
				<img src="/i/admin/del.gif" class="button" alt="Удалить" onclick="if(confirm('Что, правда удалить?')) { document.location='/admin/?module=vtree&page=1&delid=<?= $item['id'] ?>'; }">
			</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="6">
				<span class="command" onclick="ge('<?= $item['id'] ?>_children').style.display='block';">Подпапки:</span>
				<div id="<?= $item['id']?>_children" class="hidden_form">
					<table class="admin_table" cellspacing="0">
					<tr>
						<th>id</th>
						<th>Папка</th>
						<th>Модуль</th>
						<th>Действие</th>
						<th>Шаблон</th>
						<th>Доступен всем</th>
						<th>Действия</th>
					</tr>
					<? foreach($item['children'] as &$subitem): ?>
						<? draw_vtree_item(&$subitem, $_modules_installed); ?>
					<? endforeach; ?>
					<tr>
						<td>+<input type="hidden" name="<?= $item['id']?>_new_pid" id="<?= $item['id']?>_new_pid" value="<?= $item['id'] ?>"></td>
						<td>
							<input type="text" name="<?= $item['id'] ?>_new_name" id="<?= $item['id'] ?>_new_name" value="">
						</td>
						<td>
							<select name="<?= $item['id'] ?>_new_module" id="<?= $item['id'] ?>_new_module">
							<option value=""></option>
							<? foreach($_modules_installed as $module => $name): ?>
								<option value="<?= $module ?>"><?= $name ?></option>
							<? endforeach; ?>
						</select>
						</td>
						<td>
							<input type="text" name="<?= $item['id'] ?>_new_action" id="<?= $item['id'] ?>_new_action" value="">
						</td>
						<td>
							<input type="text" name="<?= $item['id'] ?>_new_template" id="<?= $item['id'] ?>_new_template" value="">
						</td>
						<td>
							<input type="checkbox" name="<?= $item['id'] ?>_new_access" id="<?= $item['id'] ?>_new_access" value="1">
						</td>
						<td>
							<img src="/i/admin/ok.gif" class="button" alt="Добавить" onclick="document.location='/admin/?module=vtree&page=1&add='+ge('<?= $item['id']?>_new_pid').value+'&n='+ge('<?= $item['id']?>_new_name').value+'&m='+ge('<?= $item['id']?>_new_module').value+'&a='+ge('<?= $item['id']?>_new_action').value+'&t='+ge('<?= $item['id']?>_new_template').value+'&s='+ge('<?= $item['id'] ?>_new_access').checked">
						</td>
					</tr>
					</table>
				</div>
			</td>
		</tr>
		<?
	}
}
?>

<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? endif; ?>

<table class="admin_table" cellspacing="0">
	<tr>
		<th>id</th>
		<th>Папка</th>
		<th>Модуль</th>
		<th>Действие</th>
		<th>Шаблон</th>
		<th>Доступен всем</th>
		<th>Действия</th>
	</tr>
	<? foreach($_vtree as &$item): ?>
		<? draw_vtree_item(&$item, $_modules_installed); ?>
	<? endforeach; ?>
</table>