<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE html>
<html>
<head>
	<title>Drunkeeper</title>
	<link rel="stylesheet" type="text/css" href="/css/drunkeeper.css" />
</head>
<body>
	<h1>Drunkeeper</h1>
	<div class="container_l">
		<div class="item plot">
			<img src="/drunkeeper/plot" />
		</div>
		<? foreach($_acts as $act): ?>
			<div class="item">
				<span class="date"><?= date('d.m.Y', $act['date_drinked']) ?></span>
				<span class="volume"><?= (int)$act['volume'] ?><span class="measure"> мл</span></span>
				<span class="volume"><?= (float)$_drinks[$act['drink_id']]['strength'] ?><span class="measure"> %</span></span>
				<div class="drink">
					<span class="name"><?= h($_drinks[$act['drink_id']]['name']) ?></span>
					<span class="type"><?= h($_drink_types[$_drinks[$act['drink_id']]['type_id']]['name']) ?></span>
					<p><?= h($act['comment']) ?></p>
				</div>
			</div>
		<? endforeach; ?>
	</div>
	<div class="container_r">
		<div class="item">
			<table class="stat">
				<tr>
					<th>Всего выпито, мл:</th>
					<td><?= round($_stats['allvolume'], 2) ?></td>
				</tr>
				<tr>
					<th>Выпито в пересчёте на 40&deg;, мл:</th>
					<td><?= round($_stats['40volume'], 2) ?></td>
				</tr>
				<tr>
					<th>Выпито в пересчёте на спирт, мл:</th>
					<td><?= round($_stats['100volume'], 2) ?></td>
				</tr>
				<tr>
					<th>Средний градус, %:</th>
					<td><?= round($_stats['median'], 2) ?></td>
				</tr>
				<tr>
					<th colspan="2">Выпито по видам бухла, мл:</th>
				</tr>
				<? foreach($_stats['volume_dtype'] as $id => $volume): ?>
					<tr>
						<td><strong><?= $_drink_types[$id]['name'] ?>:</strong></td>
						<td><?= round($volume, 2) ?></td>
					</tr>
						<? foreach($_stats['volume_d'] as $drink_id => $v): ?>
							<? if($_drinks[$drink_id]['type_id'] == $id): ?>
								<tr class="inner">
									<td><?= $_drinks[$drink_id]['name'] ?></td>
									<td><?= round($v, 2) ?></td>
								</tr>
							<? endif; ?>
						<? endforeach; ?>
					</tr>
				<? endforeach; ?>
			</table>
		</div>
        <div class="item">
	        <table class="stat">
		        <tr>
			        <th colspan="2">За последние полгода</th>
		        </tr>
		        <? foreach($_last3m as $n => $data): ?>
		            <tr>
			            <th colspan="2"><?= substr($n, 0, 4).'.'.substr($n, 4) ?></th>
		            </tr>
		            <tr>
						<td colspan="2"><strong>Суммарно, мл</strong></td>
		            </tr>
			        <tr class="inner">
				        <td>Всего выпито:</td>
				        <td><?= round($data['allvolume'], 2) ?></td>
			        </tr>
	                <tr class="inner">
	                    <td>Выпито в пересчёте на 40&deg;:</td>
	                    <td><?= round($data['40volume'], 2) ?></td>
	                </tr>
		            <tr class="inner">
		                <td>Выпито в пересчёте на спирт:</td>
		                <td><?= round($data['100volume'], 2) ?></td>
		            </tr>
	                <tr>
	                    <td colspan="2"><strong>По типам напитков, мл</strong></td>
	                </tr>
		            <? foreach($data['volume_dtype'] as $k => $volume): ?>
			            <tr class="inner">
				            <td><?= $_drink_types[$k]['name'] ?></td>
				            <td><?= round($volume, 2) ?></td>
			            </tr>
			        <? endforeach; ?>
				<? endforeach; ?>
	        </table>
	    </div>
	</div>
</body>
</html>