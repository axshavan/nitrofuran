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
	
<div class="container">
	
	<!-- сейчас в кассе -->
	<strong>Сейчас в кассе</strong><br>
	<table class="optable" cellspacing="0">
		<? foreach($_sumbycur as $id => $sum): $bOdd = !$bOdd; ?>
			<tr class="<?= ($sum < 0 ? 'exp' : 'inc' ).($bOdd ? '_odd' : '') ?>">
				<td><?= round($sum, 2) ?>&nbsp;<?= $_currencies[$id]['symbol'] ?></td>
			</tr>
		<? endforeach; ?>
	</table>
	<br>
	<!-- /сейчас в кассе -->
	
	<!-- операции за месяц -->
	<strong>Операции за месяц</strong><br>
	<table class="optable planstable" cellspacing="0">
		<tr>
			<th>Тип операции</th>
			<th>Сумма</th>
			<th>Кол-во раз</th>
			<th>Первый раз</th>
			<th>Последний раз</th>
			<th>В среднем</th>
			<th>В среднем в месяц</th>
			<th>В этом месяце ещё</th>
			<th>Считаем?</th>
			<th>Скрыть?</th>
		</tr>
		
		<!-- статистика -->
		<? foreach($_opbytype as $optype_id => $_opbycur): ?>
			<?
			
			$rowspan = sizeof($_opbycur);
			foreach($_opbycur as $currency_id => $_op)
			{
				if($_op['average_c']): $bOdd = !$bOdd; ?>
					<tr class="<?= ($_op['left_m'] < 0 ? 'exp' : 'inc').($bOdd ? '_odd' : '') ?>">
						<? if($rowspan) :?>
							<td rowspan="<?= $rowspan ?>"><?= $_optypes[$optype_id]['name'] ?></td>
						<? endif; ?>
						<td><?= $_op['sum'].'&nbsp;'.$_currencies[$currency_id]['symbol'] ?></td>
						<td><?= $_op['count'] ?></td>
						<td><?= date('Y-m-d', $_op['first_time']) ?></td>
						<td><?= date('Y-m-d', $_op['last_time']) ?></td>
						<td><?= $_op['average_c'].'&nbsp;'.$_currencies[$currency_id]['symbol'] ?></td>
						<td><?= $_op['average_m'].'&nbsp;'.$_currencies[$currency_id]['symbol'] ?></td>
						<td>
							<? if(!$_op['do_not_count']): ?>
								<strong>
							<? endif; ?>
							<?= $_op['left_m'].'&nbsp;'.$_currencies[$currency_id]['symbol'] ?>
							<? if($_op['do_not_count']): ?>
								(не считаем<?
								switch($_op['do_not_count'])
								{
									case 'ancient':  echo ', давно не было операций'; break;
									case 'service':  echo ', служебный тип'; break;
									case 'disabled':
									default: break;
								}
								?>)
							<? else: ?>
								</strong>
							<? endif; ?>
						</td>
						<? if($rowspan): ?>
							<td rowspan="<?= $rowspan ?>">
								<? if($_op['do_not_count'] == 'disabled'): ?>
									<img src="/i/kassa/add.gif" class="button" onclick="document.location='?switch=<?= $optype_id ?>'">
								<? else:  ?>
									<img src="/i/kassa/minus.gif" class="button" onclick="document.location='?switch=<?= $optype_id ?>'">
								<? endif; ?>
							</td>
							<td rowspan="<?= $rowspan ?>"><img src="/i/kassa/del.gif" class="button" onclick="if(confirm('Что, правда скрыть?')) document.location='?hide=<?= $optype_id ?>'" /></td>
						<? endif; ?>
					</tr>
				<? endif; ?>
			<?
			$rowspan = 0;
			}
			?>
		<? endforeach; ?>
		
		<!-- планы -->
		<? foreach($_plans as $_plan): $bOdd = !$bOdd; ?>
			<tr class="<?= ($_optypes[$_plan['operation_type_id']]['is_income'] ? 'inc' : 'exp').($bOdd ? '_odd' : '') ?>">
				<td><?= $_plan['name'] == $_optypes[$_plan['operation_type_id']]['name'] ? $_plan['name'] : $_plan['name'].' / '.$_optypes[$_plan['operation_type_id']]['name'] ?></td>
				<td><?= $_plan['amount'].'&nbsp;'.$_currencies[$currency_id]['symbol'] ?></td>
				<td colspan="2">&nbsp;</td>
				<td><?= $_plan['repeat'] ?></td>
				<td colspan="2">&nbsp;</td>
				<td><strong><?= $_plan['amount'].'&nbsp;'.$_currencies[$currency_id]['symbol'] ?></strong></td>
				<td colspan="2">&nbsp;</td>
			</tr>
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
				<td><?= round($_sumbycur[$currency_id], 2) ?>&nbsp;<?= $_currencies[$currency_id]['symbol'] ?></td>
				<td><?= round($sum, 2) ?>&nbsp;<?= $_currencies[$currency_id]['symbol'] ?></td>
				<td class="itogo"><?= round($_sumbycur[$currency_id] + $sum, 2) ?>&nbsp;<?= $_currencies[$currency_id]['symbol'] ?></td>
			</tr>
		<? endforeach; ?>
	</table>
	<!-- /итого -->
</div>
	
</body>
</html>