<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Drunkeeper</title>
</head>
<body>
	<table border="1">
		<tr>
			<th>Дата</th>
			<th>Что выпито</th>
			<th>Сколько</th>
			<th>Комменатрии</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach($_acts as $act): ?>
			<tr>
				<td><?= date('Y-m-d', $act['date_drinked']) ?></td>
				<td><?= $_drink_types[$_drinks[$act['drink_id']]['type_id']]['name'] ?> / <?= h($_drinks[$act['drink_id']]['name']) ?> (<?= (int)$_drinks[$act['drink_id']]['strength'] ?>%)</td>
				<td><?= (int)$act['volume'] ?></td>
				<td><?= h($act['comment']) ?></td>
				<td><a href="/drunkeeper/edit/?id=<?= (int)$act['id'] ?>">Редактировать</a></td>
			</tr>
		<? endforeach; ?>
	</table>
	<a href="/drunkeeper/edit/?id=new">Добавить</a>
</body>
</html>