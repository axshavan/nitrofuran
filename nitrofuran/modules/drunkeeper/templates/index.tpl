<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Drunkeeper</title>
	<link rel="stylesheet" type="text/css" href="/css/drunkeeper.css" />
</head>
<body>
	<h1>Drunkeeper</h1>
	<div class="container_l">
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
					<th colspan="2">Выпито по категориям бухла, мл:</th>
				</tr>
				<tr>
					<td colspan="2" id="volume_dtype_td">
						<table>
							<? foreach($_stats['volume_dtype'] as $id => $volume): ?>
								<tr>
									<td><?= $_drink_types[$id]['name'] ?>:</td>
									<td><?= round($volume, 2) ?></td>
								</tr>
							<? endforeach; ?>
						</table>
					</td>
				</tr>
				<tr>
					<th colspan="2">Выпито по видам бухла, мл:</th>
				</tr>
				<tr>
					<td colspan="2" id="volume_d_td">
						<table>
							<? foreach($_stats['volume_d'] as $id => $volume): ?>
								<tr>
									<td><?= $_drinks[$id]['name'] ?>:</td>
									<td><?= round($volume, 2) ?></td>
								</tr>
							<? endforeach; ?>
						</table>
					</td>
				</tr>
			</table>
		</div>
	</div>
</body>
</html>