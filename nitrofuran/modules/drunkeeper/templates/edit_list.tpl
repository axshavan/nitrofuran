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
            <input type="button" onclick="document.location='/drunkeeper/edit/?id=new'" value="Выпить!" />
            <table class="admin" cellspacing="0">
                <tr>
                    <th>Дата</th>
                    <th>Что выпито</th>
                    <th>Сколько</th>
                    <th>Комментарии</th>
                    <th>&nbsp;</th>
                </tr>
		        <? $bOdd = false; foreach($_acts as $act): $bOdd = !$bOdd; ?>
                <tr class="<?= $bOdd ? 'odd' : '' ?>">
                    <td><?= date('Y-m-d', $act['date_drinked']) ?></td>
                    <td><?= $_drink_types[$_drinks[$act['drink_id']]['type_id']]['name'] ?> / <?= h($_drinks[$act['drink_id']]['name']) ?> (<?= (int)$_drinks[$act['drink_id']]['strength'] ?>%)</td>
                    <td><?= (int)$act['volume'] ?></td>
                    <td><?= h($act['comment']) ?></td>
                    <td><input type="button" onclick="document.location='/drunkeeper/edit/?id=<?= (int)$act['id'] ?>'" value="Редактировать" />
                </tr>
		        <? endforeach; ?>
            </table>
	    </div>
	</div>

</body>
</html>