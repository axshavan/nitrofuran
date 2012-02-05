<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title>Drunkeeper</title>
	<link rel="stylesheet" type="text/css" href="/css/drunkeeper.css" />
</head>
<body>
	<h1>Drunkeeper</h1>
	<div class="container">
		<? foreach($_acts as $act): ?>
			<div class="item">
				<span class="date"><?= date('d.m.Y', $act['date_drinked']) ?></span>
				<span class="volume"><?= (int)$act['volume'] ?><span class="measure"> мл</span></span>
				<span class="volume"><?= (int)$_drinks[$act['drink_id']]['strength'] ?><span class="measure"> %</span></span>
				<div class="drink">
					<span class="name"><?= h($_drinks[$act['drink_id']]['name']) ?></span>
					<span class="type"><?= h($_drink_types[$_drinks[$act['drink_id']]['type_id']]['name']) ?></span>
					<p><?= h($act['comment']) ?></p>
				</div>
			</div>
			
		<? endforeach; ?>
	</div>
</body>
</html>