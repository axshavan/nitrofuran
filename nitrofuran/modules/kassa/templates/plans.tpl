<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type: text/html; charset=UTF-8">
    <title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa.js"></script>
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.3.min.js"></script>
</head>
<body>

<a class="reset" href="/kassa/">&laquo; вернуться в кассу</a>
	
<div class="container">
	
	<!-- сейчас в кассе -->
	<strong>Сейчас в кассе</strong><br>
	<table class="optable" cellspacing="0">
		<? foreach($_sumbycur as $id => $sum): $bOdd = !$bOdd; ?>
			<tr class="<?= ($sum < 0 ? 'exp' : 'inc' ).($bOdd ? '_odd' : '') ?>">
				<td><?= $sum ?>&nbsp;<?= $_currencies[$id]['symbol'] ?></td>
			</tr>
		<? endforeach; ?>
	</table>
	<br>
	<!-- /сейчас в кассе -->
	
	<!-- операции за месяц -->
	<strong>Операции за месяц</strong><br>
	<table class="optable" cellspacing="0">
		<tr>
			<th>Тип операции</th>
			<th>Валюта</th>
			<th>Сумма</th>
			<th>Кол-во раз</th>
			<th>Первый раз</th>
			<th>Последний раз</th>
			<th>Месяцев считается</th>
			<th>В среднем</th>
			<th>В среднем в месяц</th>
			<th>В этом месяце ещё</th>
			<th>&nbsp;</th>
		</tr>
		<? foreach($_opbytype as $optype_id => $_opbycur): ?>
			<? foreach($_opbycur as $currency_id => $_op): ?>
				<? if($_op['average_c']): $bOdd = !$bOdd; ?>
					<tr class="<?= ($_op['left_m'] < 0 ? 'exp' : 'inc').($bOdd ? '_odd' : '') ?>">
						<td><?= $_optypes[$optype_id]['name'] ?></td>
						<td><?= $_currencies[$currency_id]['symbol'] ?></td>
						<td><?= $_op['sum'] ?></td>
						<td><?= $_op['count'] ?></td>
						<td><?= date('Y-m-d', $_op['first_time']) ?></td>
						<td><?= date('Y-m-d', $_op['last_time']) ?></td>
						<td><?= $_op['months'] ?></td>
						<td><?= $_op['average_c'] ?></td>
						<td><?= $_op['average_m'] ?></td>
						<td>
							<? if(!$_op['do_not_count']): ?>
								<strong>
							<? endif; ?>
							<?= $_op['left_m'] ?>
							<? if($_op['do_not_count']): ?>
								(не считаем)
							<? else: ?>
								</strong>
							<? endif; ?>
						</td>
						<td></td>
					</tr>
				<? endif; ?>
			<? endforeach; ?>
		<? endforeach; ?>
	</table>
	<br>
	<!-- /операции за месяц -->
	
	<!-- итого -->
	<strong>Прогноз на конец месяца</strong><br>
	<table class="optable" cellspacing="0">
		<tr>
			<th>Сейчас</th>
			<th>Изменение</th>
			<th>Итог</th>
		</tr>
		<? foreach($_result_sum as $currency_id => $sum): $bOdd = !$bOdd; ?>
			<tr class="<?= ($sum < 0 ? 'exp' : 'inc').($bOdd ? '_odd' : '') ?>">
				<td><?= $_sumbycur[$currency_id] ?>&nbsp;<?= $_currencies[$currency_id]['symbol'] ?></td>
				<td><?= $sum ?>&nbsp;<?= $_currencies[$currency_id]['symbol'] ?></td>
				<td class="itogo"><?= $_sumbycur[$currency_id] + $sum ?>&nbsp;<?= $_currencies[$currency_id]['symbol'] ?></td>
			</tr>
		<? endforeach; ?>
	</table>
	<!-- /итого -->
</div>
	
</body>
</html>