<link rel="stylesheet" type="text/css" href="/css/vtree.css">
<script type="text/javascript" src="/js/jquery-1.4.3.min.js"></script>

<?
if(!function_exists('draw_vtree_item'))
{
	// функция для рекурсивной отрисовки дерева
	function draw_vtree_item(&$item, $bLast, $_modules_installed)
	{
		?>
		<li class="<?= $bLast ? 'last' : '' ?>">
			<img src="/i/vtree/folder.gif">&nbsp;<span class="name" onclick="$('#data<?= $item['id'] ?>').slideToggle();"><?= $item['id'] > 1 ? $item['data']['name'] : '/' ?></span>
			<div class="data" id="data<?= $item['id'] ?>">
				<form action="/admin/?module=vtree&page=1" method="post" id="form_<?= $item['id'] ?>">
					<input type="hidden" name="" value="<?= $item['id'] ?>" id="id_<?= $item['id'] ?>">
					<table class="admin_table" cellspacing="0">
						<tr>
							<td><label for="name<?= $item['id'] ?>">Название</label></td>
							<td><input type="text" id="name<?= $item['id'] ?>" name="n" value="<?= $item['data']['name'] ?>"></td>
						</tr>
						<tr>
							<td><label for="module<?= $item['id'] ?>">Модуль</label></td>
							<td>
								<select name="m" id="module<?= $item['id'] ?>">
									<option value=""<?= $item['data']['module'] ? '' : ' selected' ?>></option>
									<? foreach($_modules_installed as $module => $name): ?>
										<option value="<?= $module ?>"<?= $item['data']['module'] == $module ? ' selected' : '' ?>><?= $name ?></option>
									<? endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<td><label for="template<?= $item['id'] ?>">Шаблон</label></td>
							<td><input type="text" id="template<?= $item['id'] ?>" name="t" value="<?= $item['data']['template'] ?>"></td>
						</tr>
						<tr>
							<td><label for="action<?= $item['id'] ?>">Действие</label></td>
							<td><input type="text" id="action<?= $item['id'] ?>" name="a" value="<?= $item['data']['action'] ?>"></td>
						</tr>
						<tr>
							<td><label for="access<?= $item['id'] ?>">Доступен всем</label></td>
							<td><input type="checkbox" id="access<?= $item['id'] ?>" name="s" value="true"<?= $item['data']['access'] ? ' checked="checked"' : '' ?>"></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input type="button" value="Сохранить" onclick="ge('id_<?= $item['id'] ?>').name='saveid'; ge('form_<?= $item['id'] ?>').submit();" />
								<input type="button" value="Добавить подпапку" onclick="ge('id_<?= $item['id'] ?>').name='add'; ge('form_<?= $item['id'] ?>').submit();" />
							</td>
						</tr>
					</table>
				</form>
			</div>
			<? if(sizeof($item['children'])): ?>
				<ul class="ul-tree">
					<? foreach($item['children'] as &$subitem): ?>
						<? draw_vtree_item($subitem, !($a = prev($item['children'])), $_modules_installed); ?>
					<? endforeach; ?>
				</ul>
			<? endif; ?>
		</li>
		<?
	}
}
?>

<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? endif; ?>
<ul class="ul-root">
	<? foreach($_vtree as &$item): ?>
		<? draw_vtree_item($item, false, $_modules_installed); ?>
	<? endforeach; ?>
</ul>