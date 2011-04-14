<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa.js"></script>
</head>
<body>
	
<a class="reset" href="/kassa/">&laquo; вернуться в кассу</a>
	
<div class="container stats_graph">
	<form action="." id="stats_graph_form" method="post">
		<input type="hidden" name="draw" value="1">
		<table>
			<tr>
				<td><label for="inp_comment">Комментарий</label></td>
				<td><label for="inp_optype">Тип операции</label></td>
				<td><label for="inp_account">Счёт</label></td>
				<td><label for="inp_currency">Валюта</label></td>
			</td>
			<tr>
				<td><input type="text" id="inp_comment" name="comment" value="<?= h($_REQUEST['comment']) ?>"></td>
				<td>
					<select name="optype" id="inp_optype">
						<option value=""<?= $_REQUEST['optype'] ? '' : ' selected' ?>></option>
						<? foreach($_optypes_by_id as $op): ?>
							<option value="<?= $op['id'] ?>"<?= $_REQUEST['optype'] == $op['id'] ? ' selected' : '' ?>><?= $op['name'] ?></option>
						<? endforeach; ?>
					</select>
				</td>
				<td>
					<select name="account" id="inp_account">
						<option value=""<?= $_REQUEST['account'] ? '' : ' selected' ?>></option>
						<? foreach($_accounts as $acc): ?>
							<option value="<?= $acc['id'] ?>"<?= $_REQUEST['account'] == $acc['id'] ? ' selected' : '' ?>><?= $acc['name'] ?></option>
						<? endforeach; ?>
					</select>
				</td>
				<td>
					<select name="currency" id="inp_currency">
						<option value=""<?= $_REQUEST['currency'] ? '' : ' selected' ?>></option>
						<? foreach($_currencies as $cur): ?>
							<option value="<?= $cur['id'] ?>"<?= $_REQUEST['currency'] == $cur['id'] ? ' selected' : '' ?>><?= $cur['name'] ?></option>
						<? endforeach; ?>
					</select>
				</td>
			</tr>
		</table>
		Фильтрация по дате (YYYY-MM-DD):<br>
		<input type="text" name="filter_from" id="filter_from" value="<?= $filter_from ?>"> &mdash; <input type="text" name="filter_to" id="filter_to" value="<?= $filter_to ?>">
		<span class="command" onclick="stat_period2inputs('week');">неделя</span>
		<span class="command" onclick="stat_period2inputs('month');">месяц</span>
		<span class="command" onclick="stat_period2inputs('quartal');">квартал</span>
		<span class="command" onclick="stat_period2inputs('year');">год</span><br>
		<input type="checkbox" name="weekaverage" id="weekaverage" value="1"<?= $weekaverage ? ' checked' : '' ?>> <label for="weekaverage">Строить среднее по неделям</label><br>
		<input type="submit" value="Построить выборку"> <input type="button" value="Сбросить" onclick="document.location=document.location;">
	</form>
	
	<!-- картинка-->
	<? if($image_src): ?>
		<image class="stats_graph_image" src="<?= $image_src ?>">
	<? endif; ?>
</div>

</body>
</html>