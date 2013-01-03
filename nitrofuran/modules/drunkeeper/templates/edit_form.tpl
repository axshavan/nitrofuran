<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Drunkeeper</title>
	<link rel="stylesheet" type="text/css" href="/css/drunkeeper.css" />
	<script type="text/javascript" src="/js/drunkeeper.js"></script>
</head>
<body>
	<h1>Drunkeeper</h1>
	<div class="container_form">
		<div class="item">
			<form action="/drunkeeper/edit/" method="post">
				<input type="hidden" name="id" value="<?= (int)$id ?>">
				<table>
					<tr>
						<td>Тип напитка:</td>
						<td>
							<select id="drunkeeper_form_drinktype" name="drinktype" onchange="onDrinkTypeChange(this.value)">
								<option value="" <?= $id ? '' : 'selected="selected"' ?>></option>
								<? foreach($_drink_types as $type): ?>
									<option value="<?= $type['id'] ?>" <?= $type['id'] == $_drinks[$_act['drink_id']]['type_id'] ? 'selected="selected"' : '' ?>><?= $type['name'] ?></option>
								<? endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<? foreach($_drink_types as $type): ?>
								<div class="drink2" id="drunkeeper_form_drink<?= $type['id'] ?>_div"<?= $_drinks[$_act['drink_id']]['type_id'] == $type['id'] ? ' style="display: block"' : '' ?>>
									<?= $type['name'] ?>:
									<select id="drunkeeper_form_drink<?= $type['id'] ?>_select" name="drink_<?= $type['id'] ?>">
										<option value="" <?= $id ? '' : 'selected="selected"' ?>></option>
										<? foreach($type['drinks'] as $drink): ?>
											<option value="<?= $drink['id'] ?>" <?= $_act['drink_id'] == $drink['id'] ? 'selected="selected"' : '' ?>><?= $drink['name'].' '.$drink['strength'].'%' ?></option>
										<? endforeach; ?>
									</select>
								</div>
							<? endforeach; ?>
						</td>
					</tr>
					<tr>
						<td>Количество:</td>
						<td>
							<input type="text" id="drunkeeper_form_volume" name="volume" value="<?= (int)$_act['volume'] ?>">
						</td>
					</tr>
					<tr>
						<td>Дата:</td>
						<td>
							<table>
								<tr>
									<td>число</td><td><input type="text" id="drunkeeper_form_day" name="day" value="<?= date('j', $_act['date_drinked'] ? $_act['date_drinked'] : time()) ?>"></td>
								</tr>
								<tr>
									<td>месяц</td><td><input type="text" id="drunkeeper_form_month" name="month" value="<?= date('n', $_act['date_drinked'] ? $_act['date_drinked'] : time()) ?>"></td>
								</tr>
								<tr>
									<td>год</td><td><input type="text" id="drunkeeper_form_year" name="year" value="<?= date('Y', $_act['date_drinked'] ? $_act['date_drinked'] : time()) ?>"><br></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td>Комментарий:</td>
						<td>
							<textarea id="drunkeeper_form_comment" name="comment"><?= h($_act['comment']) ?></textarea>
						</td>
					</tr>
				</table>
				<input type="submit" value="Сохранить">
			</form>
			<? if($id): ?>
				<form action="/drunkeeper/edit/" method="post">
					<input type="hidden" name="delete" value="<?= (int)$id ?>">
					<input type="submit" value="Удалить">
				</form>
			<? endif; ?>
		</div>
	</div>
</body>
</html>