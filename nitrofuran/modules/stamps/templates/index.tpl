<!DOCTYPE html>
<html>
<head>
    <title>Марки</title>
    <link rel="stylesheet" type="text/css" href="/css/stamps.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div class="head">
    <a href="/stamps/import">Провести реимпорт</a>
</div>
<form action="/stamps/" method="get" id="filter-form">
	<select name="country" id="country" onchange="document.getElementById('filter-form').submit()">
		<option value="">(все)</option>
		<? foreach($_countries as $v): ?>
			<option value="<?= htmlspecialchars($v) ?>" <?= isset($_REQUEST['country']) && $_REQUEST['country'] == $v ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v) ?></option>
		<? endforeach; ?>
	</select>
	<select name="year" id="year" onchange="document.getElementById('filter-form').submit()">
		<option value="">(все)</option>
		<? foreach($_years as $v): ?>
			<option value="<?= htmlspecialchars($v) ?>" <?= isset($_REQUEST['year']) && $_REQUEST['year'] == $v ? 'selected="selected"' : '' ?>><?= htmlspecialchars($v) ?></option>
		<? endforeach; ?>
	</select>
	<select name="book_id" id="book_id" onchange="document.getElementById('filter-form').submit()">
		<option value="">(все)</option>
		<? foreach($_book_ids as $v): ?>
			<option value="<?= htmlspecialchars($v) ?>" <?= isset($_REQUEST['book_id']) && $_REQUEST['book_id'] == $v ? 'selected="selected"' : '' ?>><?= $v ?> - <?= htmlspecialchars($_book_names[$v]) ?></option>
		<? endforeach; ?>
	</select>
	<a href="/stamps">Сбросить фильтр</a>
</form>
Марок: <?= sizeof($_data) ?>
<div class="data">
	<table>
		<? foreach($_headers as $h): ?>
			<th>
				<?= $h['name'] ?>
				<a href="<?= $filter_string ?>sort=<?= $h['code'] ?>&dir=asc">&darr;</a>
				<a href="<?= $filter_string ?>sort=<?= $h['code'] ?>&dir=desc">&uarr;</a>
			</th>
		<? endforeach; ?>
		<? foreach($_data as $row): ?>
			<tr>
				<? foreach($_headers as $h): ?>
					<td><?= isset($h['boolean']) && $h['boolean'] ? ($row[$h['code']] ? 'Да' : 'Нет' ) : htmlspecialchars($row[$h['code']]) ?></td>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</table>
</div>
</body>
</html>