<link rel="stylesheet" type="text/css" href="/css/static.css">

<?
if(!function_exists('draw_vtree_item'))
{
	// функция для рекурсивной отрисовки дерева
	function draw_vtree_item(&$item, $bLast, $_modules_installed)
	{
		?>
		<li class="<?= $bLast ? 'last' : '' ?>">
			<span class="name"><?= $item['id'] > 1 ? $item['data']['name'] : '/' ?></span>
			<div class="data" id="data<?= $item['id'] ?>">
				Модуль: <?= $_modules_installed[$item['data']['module']] ?>
				<? if($item['data']['static_page_id']): ?>
					<a href="?module=static&page=3&pageid=<?= $item['data']['static_page_id'] ?>"><!--Редактировать содержимое--></a>
				<? endif; ?>
				<br>Действие/шаблон: <?= $item['data']['action'] ? $item['data']['action'] : '-' ?>  / <?= $item['data']['template'] ? $item['data']['template'] : '-' ?>
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