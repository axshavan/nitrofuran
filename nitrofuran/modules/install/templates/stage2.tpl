<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8">
	<title>Установка NitroFuran, stage 2</title>
	<style type="text/css">
		body { background-color: #f0f0f0; font-family: sans-serif; }
		div.main { padding: 10px; background-color: white; border: 1px solid grey; }
		input[type=text] { border: 1px solid grey; padding: 3px; background-color: white; }
		input[type=submit] { border: 1px solid grey; padding: 3px; background-color: silver; cursor: pointer; }
		p { width: 70%; }
		table td { padding: 4px; }
		table td:first-child { text-align: right; }
		div.error { background-color: #FFA0A0; color: maroon; border: 1px solid red; padding: 20px; font-weight: bold; }
	</style>
</head>
<body>
	<h1>Установка NitroFuran, stage 2</h1>
	<div class="main">
		<? if($error_text): ?>
			<div class="error">
				<?= $error_text ?>
			</div>
		<? else: ?>
			<p>
				Помимо собственно движка сайта, для работы необходимы модули.
				Они базируются на движке и обычно не взаимосвязаны друг с другом.
				Ниже перечислены модули, которые могут быть установлены или
				переустановлены (в том случае, если они уже установлены).
			</p>
			<p>
				После установки вы будете перенаправлены на главную страницу сайта;
				скорее всего, там ничего не будет. Чтоб попасть в административную
				часть, пройдлите потом по ссылке<br>
				http://<?= $_SERVER['HTTP_HOST'] ?>/admin/<br>
				Если скрипт не сможет (по причине отсутствия прав на запись или
				по какой-либо иной) исправить файл .htaccess в корне сайта,
				исправьте его вручную: раскомментируйте строки в нём.
			</p>
			<p>
				Настоятельно рекомендуется установить модуль update.
			</p>
		<? endif; ?>
		<form action="install.php?stage=3" method="post">
			<table>
			<? foreach($_modules as $module): ?>
				<tr>
					<td><?= $module['name'] ?></td>
					<td><input type="checkbox" id="cb_<?= $module['name'] ?>" name="install_<?= $module['name'] ?>"<?= $module['installed'] ? '' : ' checked' ?>> <label for="cb_<?= $module['kassa'] ?>"><?= $module['installed'] ? 'Переустановить' : 'Установить'?></label></td>
				</tr>
			<? endforeach; ?>
			<tr>
				<td></td>
				<td>
					<input type="submit" value="Зафигарить">
				</td>
			</table>
		</form>
	</div>
</body>
</html>