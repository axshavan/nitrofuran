<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Drunkeeper</title>
    <link rel="stylesheet" type="text/css" href="/css/drunkeeper.css" />
    <script type="text/javascript" src="/js/drunkeeper.js"></script>
</head>
<body>
<h1>Drunkeeper</h1>
	<div class="container_form">
        <div class="item">
            <a href="/drunkeeper/edit/?id=new">Выпить!</a>
            <table class="admin" cellspacing="0">
                <tr>
                    <th>Дата</th>
                    <th>Что выпито</th>
                    <th>Сколько</th>
                    <th>Комментарии</th>
                    <th>&nbsp;</th>
                </tr>
		        <? foreach($_acts as $act): $bOdd = !$bOdd; ?>
                <tr class="<?= $bOdd ? 'odd' : '' ?>">
                    <td><?= date('Y-m-d', $act['date_drinked']) ?></td>
                    <td><?= $_drink_types[$_drinks[$act['drink_id']]['type_id']]['name'] ?> / <?= h($_drinks[$act['drink_id']]['name']) ?> (<?= (int)$_drinks[$act['drink_id']]['strength'] ?>%)</td>
                    <td><?= (int)$act['volume'] ?></td>
                    <td><?= h($act['comment']) ?></td>
                    <td><a href="/drunkeeper/edit/?id=<?= (int)$act['id'] ?>">Редактировать</a></td>
                </tr>
		        <? endforeach; ?>
            </table>
	    </div>
	</div>

</body>
</html>