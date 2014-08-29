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

<div class="container">
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
			<? foreach($_sum_by_optypes[$gid] as $oname => $_optype): ?>
				<tr>
					<td><?= $oname ?></td>
					<? foreach($_used_currencies as $c): ?>
                        <td><?= number_format($_optype[$c], 2, '.', ' ') ?></td>
					<? endforeach; ?>
				</tr>
			<? endforeach; ?>
		<? endforeach; ?>
	</table>
</div>

<? trace($_sum_by_accounts) ?>
<? trace($_sum) ?>

</body>
</html>
