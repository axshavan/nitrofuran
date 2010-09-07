<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type: text/html; charset=UTF-8">
    <title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa.js"></script>
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.2.min.js"></script>
</head>
<body>

<a class="reset" href="/kassa/">&laquo; вернуться в кассу</a>

<div class="stats-column-1">
	
	<!-- суммы прихода и расхода по месяцам -->
	<div class="container">
		<strong>Суммы по месяцам</strong>
		<table cellspacing="0" class="stat_table">
			<tr>
				<th>Месяц</th>
				<th>Приход</th>
				<th>Расход</th>
				<th>Итого</th>
			</tr>
			<? foreach($_months as $_m): $bOdd = !$bOdd; ?>
				<tr<?= $bOdd ? ' class="odd"' : '' ?>>
					<td><?= $_m['name'] ?></td>
					<td><?= $_m['income'] ?></td>
					<td><?= $_m['expenditure'] ?></td>
					<td><?= $_m['income'] - $_m['expenditure'] ?></td>
				</tr>
			<? endforeach; ?>
		</table>
	</div>
	<!-- /суммы прихода и расхода по месяцам -->
	
</div>

<div class="stats-column-2">
	
	<!-- разбивка максимумов по типам операций и валютам -->
	<div class="container">
		<strong>Максимумы по операциям</strong>
		<table class="optable" cellspacing="0">
			<tr>
				<th>Тип операции</th>
				<th>Значение</th>
				<th>Дата операции</th>
				<th>Комментарий</th>
			</tr>
			<? foreach($_operation_max as $v): ?>
				<? foreach($v as $_op): $bOdd = !$bOdd; ?>
					<tr class="<?= ($_optypes[$_op['type_id']]['is_income'] ? 'inc' : 'exp').($bOdd ? '_odd' : '') ?>">
						<td><?= $_optypes[$_op['type_id']]['name'] ?></td>
						<td><?= $_op['amount'] ?>&nbsp;<?= $_currencies[$_op['currency_id']]['symbol'] ?></td>
						<td><?= rudate('d M Y H:i', $_op['time']) ?></td>
						<td><?= $_op['comment'] ?></td>
					</tr>
				<? endforeach; ?>
			<? endforeach; ?>
		</table>
	</div>
	<!-- разбивка максимумов по типам операций и валютам -->
	
</div>

<div class="stats-column-3">
	<div class="container">
		
		<!-- сводная статистика по операциям -->
		<strong>Сводная статистика по операциям</strong>
		<p><strong>max</strong> &mdash; максимальное значение, <strong>&sum;</strong>
		&mdash; сумма операций данного типа, <strong>cnt</strong> &mdash; количество
		операций данного типа, <strong>~</strong> &mdash; средняя сумма операции.</p>
		<table class="optable" cellspacing="0">
			<tr>
				<th>Операция</th>
				<th>max</th>
				<th>&sum;</th>
				<th>cnt</th>
				<th>~</th>
			</tr>
			<? foreach($_optypes_g as $g => $_group): ?>
				<tr>
					<th colspan="5"><?= $_optypegroups[$g]['name'] ?></th>
				</tr>
				<? foreach($_group as $_optype): $bOdd = !$bOdd; ?>
					<tr class="<?= ($_optype['is_income'] ? 'inc' : 'exp').($bOdd ? '_odd' : '') ?>">
						<td><?= $_optype['name'] ?></td>
						<td>
							<? foreach($_operation_max[$_optype['id']] as $c => $v): ?>
								<?= $v['amount'] ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_operation_sum[$_optype['id']] as $c => $v): ?>
								<?= $v ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_operation_count[$_optype['id']] as $c => $v): ?>
								<?= $_currencies[$c]['symbol'] ?>&mdash;<?= $v ?><br>
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_operation_sum[$_optype['id']] as $c => $v): ?>
								<?= round($v / $_operation_count[$_optype['id']][$c], 2) ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
					</tr>
				<? endforeach; ?>
			<? endforeach; ?>
		</table>
		<!-- /сводная статистика по операциям -->
		
	</div>
</div>

</body>
</html>