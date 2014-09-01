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
<a class="reset" href="/kassa/stats">&laquo; вернуться в статистику</a>

<div class="container mb15">
	<strong>Итоги за месяц по типам операций</strong>
	<table class="optable planstable" cellspacing="0">
        <tr>
            <th>&nbsp;</th>
			<? foreach($_used_currencies as $c): ?>
	            <th><?= $c ?></th>
			<? endforeach; ?>
        </tr>
		<? foreach($_sum_by_optypegroups as $gid => $_group): ?>
			<tr>
				<th><?= $_optypegroups[$gid]['name'] ?></th>
				<? foreach($_used_currencies as $c): ?>
					<th><?= number_format($_group[$c], 2, '.', ' ') ?></th>
				<? endforeach; ?>
			</tr>
			<? foreach($_sum_by_optypes[$gid] as $oname => $_optype): $bOdd = !$bOdd; ?>
				<tr class="<?= ($_group[$c] > 0 ? 'inc' : 'exp').($bOdd ? '_odd' : '') ?>">
					<td><?= $oname ?></td>
					<? foreach($_used_currencies as $c): ?>
                        <td><?= number_format($_optype[$c], 2, '.', ' ') ?></td>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
		<? endforeach; ?>
	</table>
</div>

<div class="container mb15">
	<strong>Итоги за месяц по различным счетам в кассе</strong>
    <table class="optable" cellspacing="0">
	    <? foreach($_sum_by_accounts as $account => $sums): $tr0 = true; $bOdd = !$bOdd; ?>

		        <? foreach($sums as $k => $v): $bOdd = !$bOdd; ?>
		            <?
		            if($tr0)
		            {
			            echo '<tr class="'.($v > 0 ? 'inc' : 'exp').($bOdd ? '_odd' : '').'"><td rowspan="'.sizeof($sums).'">'.$account.'</td>';
			            $tr0 = false;
		            }
		            else
		            {
			            echo '<tr class="'.($v > 0 ? 'inc' : 'exp').($bOdd ? '_odd' : '').'">';
		            }
		            ?><td><?= $v.'&nbsp;'.$k ?></td></tr>
		        <? endforeach; ?>
	        </tr>
		<? endforeach; ?>
	</table>
    <strong>Общий итог за месяц по валютам</strong>
    <table class="optable" cellspacing="0">
		<? foreach($_sum as $k => $v): $bOdd = !$bOdd; ?>
        <tr class="<?= ($v > 0 ? 'inc': 'exp').($bOdd ? '_odd' : '') ?>">
            <td><?= $k ?></td>
            <td><?= $v ?></td>
        </tr>
		<? endforeach; ?>
    </table>
</div>

</body>
</html>
