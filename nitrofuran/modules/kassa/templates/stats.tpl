<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?= $title ?></title>
    <link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa<?= $use_blue_template ? '_blue' : '' ?>.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa.js"></script>
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.3.min.js"></script>
</head>
<body class="p5">

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
			<? foreach($_months as $_m): $bOdd = !$bOdd; $month_sum = array(); ?>
					<tr<?= $bOdd ? ' class="odd"' : '' ?>>
						<td><?= $_m['name'] ?></td>
						<td>
							<? foreach($_m['income'] as $currency => $sum): $month_sum[$currency] += $sum; ?>
								<?= round($sum, 2) ?>&nbsp;<?= $currency ?><br />
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_m['expenditure'] as $currency => $sum): $month_sum[$currency] -= $sum; ?>
								<?= round($sum, 2) ?>&nbsp;<?= $currency ?><br />
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($month_sum as $currency => $sum): ?>
								<?= round($sum, 2) ?>&nbsp;<?= $currency ?><br />
							<? endforeach; ?>
						</td>
					</tr>
				
			<? endforeach; ?>
		</table>
	</div>
	<!-- /суммы прихода и расхода по месяцам -->
	
	<!-- статистика за последний 31 день -->
	<div class="container">
		<strong>За последний 31 день</strong>
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
				<? foreach($_group as $_optype): ?>
					<?
						if(
						   !sizeof($_operation_max_m[$_optype['id']]) &&
						   !sizeof($_operation_count_m[$_optype['id']]) &&
						   !sizeof($_operation_sum_m[$_optype['id']])
						)
						{
							continue;
						}
						$bOdd = !$bOdd;
					?>
					<tr class="<?= ($_optype['is_income'] ? 'inc' : 'exp').($bOdd ? '_odd' : '') ?>">
						<td><?= $_optype['name'] ?></td>
						<td>
							<? foreach($_operation_max_m[$_optype['id']] as $c => $v): ?>
								<?= $v['amount'] ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_operation_sum_m[$_optype['id']] as $c => $v): ?>
								<?= $v ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_operation_count_m[$_optype['id']] as $c => $v): ?>
								<?= $_currencies[$c]['symbol'] ?>&mdash;<?= $v ?><br>
							<? endforeach; ?>
						</td>
						<td>
							<? foreach($_operation_sum_m[$_optype['id']] as $c => $v): ?>
								<?= round($v / $_operation_count_m[$_optype['id']][$c], 2) ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
					</tr>
				<? endforeach; ?>
			<? endforeach; ?>
		</table>
	</div>
	<!-- /статистика за последний 31 день -->

	<!-- статистика по дням недели -->
	<?
	$_weekdaynames = array
	(
		1 => 'Понедельник',
		2 => 'Вторник',
		3 => 'Среда',
		4 => 'Четверг',
		5 => 'Пятница',
		6 => 'Суббота',
		7 => 'Воскресенье'
	);
	?>
    <div class="container">
		<strong>Статистика по дням недели</strong>
		<table class="stat_table" cellspacing="0">
			<? for($daynum = 1; $daynum <= 7; $daynum++): $bOdd = !$bOdd; ?>
				<tr class="<?= $bOdd ? 'odd' : '' ?>">
					<td rowspan="<?= sizeof($_weekdays[$daynum]['opnum_c']) + 1 ?>"><?= $_weekdaynames[$daynum] ?></td>
					<td colspan="2"><strong>Всего операций:</strong></td>
					<td><strong><?= $_weekdays[$daynum]['opnum'] ?></strong></td>
					<td><strong>~</strong></td>
				</tr>
				<? foreach($_weekdays[$daynum]['opnum_c'] as $currency_id => $v): $bOdd = !$bOdd; ?>
					<tr class="<?= $bOdd ? 'odd' : '' ?>">
						<td><?= $_currencies[$currency_id]['name'].' ('.$_currencies[$currency_id]['symbol'].')' ?></td>
                        <td><?= round($_weekdays[$daynum]['opsum_c'][$currency_id], 2).' '.$_currencies[$currency_id]['symbol'] ?></td>
						<td><?= $v ?></td>
						<td><?= round($_weekdays[$daynum]['opsum_c'][$currency_id] / $v, 2).' '.$_currencies[$currency_id]['symbol'] ?></td>
					</tr>
				<? endforeach; ?>
			<? endfor; ?>
		</table>
	</div>
	<!-- /статистика по дням недели -->

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
	
	<!-- сводная статистика по операциям -->
	<div class="container">
		<strong>Сводная статистика по операциям</strong>
		<p><strong>max</strong> &mdash; максимальное значение, <strong>&sum;</strong>
		&mdash; сумма операций данного типа, <strong>cnt</strong> &mdash; количество
		операций данного типа, <strong>~</strong> &mdash; средняя сумма операции,
		<strong>~m</strong> &mdash; средняя сумма операций за месяц.</p>
		<table class="optable" cellspacing="0">
			<tr>
				<th>Операция</th>
				<th>max</th>
				<th>&sum;</th>
				<th>cnt</th>
				<th>~</th>
				<th>~m</th>
			</tr>
			<? foreach($_optypes_g as $g => $_group): ?>
				<tr>
					<th colspan="6"><?= $_optypegroups[$g]['name'] ?></th>
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
						<td>
							<? foreach($_operation_sum[$_optype['id']] as $c => $v): ?>
								<?= round($v / $kassa_action_time, 2) ?>&nbsp;<?= $_currencies[$c]['symbol'] ?><br>
							<? endforeach; ?>
						</td>
					</tr>
				<? endforeach; ?>
			<? endforeach; ?>
		</table>
	</div>
	<!-- /сводная статистика по операциям -->
	
	<!-- статистика по комментариям -->
	<div class="container">
		<strong>Топ-10 комментариев по сумме операций в разных валютах</strong><br />
		<? foreach($_comments_max_sum as $c => &$v): ?>
			<?= $_currencies[$c]['name'].' '.$_currencies[$c]['symbol'] ?>
			<table class="optable" cellspacing="0">
				<? foreach($v as $k2 => &$v2): $bOdd = !$bOdd; ?>
					<tr class="<?= $bOdd ? 'odd' : 'notodd' ?>">
						<td><?= $k2 ?></td>
						<td><?= $v2['sum'].'&nbsp;'.$_currencies[$c]['symbol'] ?></td>
					</tr>
				<? endforeach; ?>
			</table>
			<br />
		<? endforeach; ?>
		<br />
		<strong>Топ-10 комментариев по количеству операций в разных валютах</strong><br />
		<? foreach($_comments_max_quantity as $c => &$v): ?>
			<?= $_currencies[$c]['name'].' '.$_currencies[$c]['symbol'] ?>
			<table class="optable" cellspacing="0">
				<? foreach($v as $k2 => &$v2): $bOdd = !$bOdd; ?>
					<tr class="<?= $bOdd ? 'odd' : 'notodd' ?>">
						<td><?= $k2 ?></td>
						<td><?= $v2['quantity'] ?></td>
					</tr>
				<? endforeach; ?>
			</table>
			<br />
		<? endforeach; ?>
		<br />
		<strong>Топ-10 комментариев по средней сумме операций в разных валютах</strong><br />
		<? foreach($_comments_max_average as $c => &$v): ?>
			<?= $_currencies[$c]['name'].' '.$_currencies[$c]['symbol'] ?>
			<table class="optable" cellspacing="0">
				<? foreach($v as $k2 => &$v2): $bOdd = !$bOdd; ?>
					<tr class="<?= $bOdd ? 'odd' : 'notodd' ?>">
						<td><?= $k2 ?></td>
						<td><?= $v2['average'].'&nbsp;'.$_currencies[$c]['symbol'] ?></td>
					</tr>
				<? endforeach; ?>
			</table>
			<br />
		<? endforeach; ?>
	</div>
	<!-- /статистика по комментариям -->
</div>

</body>
</html>