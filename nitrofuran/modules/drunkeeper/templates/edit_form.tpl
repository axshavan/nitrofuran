<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Drunkeeper &alpha;-version</title>
</head>
<body>
	<form action="/drunkeeper/edit/" method="post">
		<input type="hidden" name="id" value="<?= (int)$id ?>">
		Тип напитка:
		<select id="drunkeeper_form_drinktype" name="drinktype">
			<option value="" <?= $id ? '' : 'selected="selected"' ?>></option>
			<? foreach($_drink_types as $type): ?>
				<option value="<?= $type['id'] ?>" <?= $type['id'] == $_drinks[$_act['drink_id']]['type_id'] ? 'selected="selected"' : '' ?>><?= $type['name'] ?></option>
			<? endforeach; ?>
		</select>
		<br>
		<? foreach($_drink_types as $type): ?>
			<div id="drunkeeper_form_drink<?= $type['id'] ?>_div">
				<?= $type['name'] ?>:
				<select id="drunkeeper_form_drink<?= $type['id'] ?>_select" name="drink_<?= $type['id'] ?>">
					<option value="" <?= $id ? '' : 'selected="selected"' ?>></option>
					<? foreach($type['drinks'] as $drink): ?>
						<option value="<?= $drink['id'] ?>" <?= $_act['drink_id'] == $drink['id'] ? 'selected="selected"' : '' ?>><?= $drink['name'].' '.$drink['strength'].'%' ?></option>
					<? endforeach; ?>
				</select>
			</div>
		<? endforeach; ?>
		Количество: <input type="text" id="drunkeeper_form_volume" name="volume" value="<?= (int)$_act['volume'] ?>"><br>
		Дата: число <input type="text" id="drunkeeper_form_day" name="day" value="<?= date('j', $_act['date_drinked']) ?>">
		месяц <input type="text" id="drunkeeper_form_month" name="month" value="<?= date('n', $_act['date_drinked']) ?>">
		год <input type="text" id="drunkeeper_form_year" name="year" value="<?= date('Y', $_act['date_drinked']) ?>"><br>
		Комментарий: <textarea id="drunkeeper_form_comment" name="comment"><?= h($_act['comment']) ?></textarea><br>
		<input type="submit" value="Сохранить">
	</form>
	<? if($id): ?>
		<form action="/drunkeeper/edit/" method="post">
			<input type="hidden" name="delete" value="<?= (int)$id ?>">
			<input type="submit" value="Удалить">
		</form>
	<? endif; ?>
</body>
</html>