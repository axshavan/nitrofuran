<?

/**
 * Сформировать дерево папок рекурсивно в виде плоского списка
 * @param  array  $item массив с деревом папок
 * @param  string $path служебная переменная - текущий путь при рекурсивной отрисовке
 * @return array
 */
function get_plain_vtree(&$item, $path = '/')
{
	$_result = array();
	$_result[$item['id']] = $item['id'] ? $path.$item['data']['name'] : '<em>не указано</em>';
	foreach($item['children'] as $subitem)
	{
		$_result = $_result + get_plain_vtree($subitem, $item['data']['name'] ? $path.$item['data']['name'].'/' : $path);
	}
	return $_result;
}
$vtree = get_plain_vtree($vtree);

?>
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
                <td><?= h($userlist[$blog['user_id']]['login'].' '.($userlist[$blog['user_id']]['full_name'] ? '('.$userlist[$blog['user_id']]['login'].')' : '')) ?></td>
                <td><?= $vtree[$blog['tree_id']] ?></td>
                <td>
	                <a href="?page=blogedit&id=<?= $blog['id'] ?>"><img src="/i/admin/edit.gif" /></a>
                    <a href="?page=blogdelete&id=<?= $blog['id'] ?>" onclick="return confirm('Правда удалить?')"><img src="/i/admin/del.gif" /></a>
                </td>
			</tr>
		<? endforeach; ?>
	</table>
<? else: ?>
	<div class="error">Нет блогов</div>
<? endif;?>