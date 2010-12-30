<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8">
	<title>Установка NitroFuran, stage 0</title>
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
	<h1>Установка NitroFuran, stage 0</h1>
	<div class="main">
		<? if($error_text): ?>
			<div class="error">
				<?= $error_text ?>
			</div>
		<? else: ?>
			<p>
				Движки многих сайтов для хранения информации используют базы данных,
				и этот &mdash; не исключение. Скрипт не смог подключиться к серверу
				баз данных MySQL с использованием указанных в настройках параметров,
				или смог, но не нашёл там основных таблиц, наличие которых
				свидетельствовало бы о том, что он уже установлен.
			</p>
			<p>
				Поэтому ниже вам предлагается ввести адрес и название базы данных,
				логин и пароль пользователя, чтоб к ней подключаться, и, при необходимости,
				пароль на рута. Рутовый пароль нигде храниться не будет и будет
				использован только при инсталляции.
			</p>
			<p>
				Скрипт инсталляции попробует переписать существующий конфигурационный файл
				(&lt;корень сайта&gt;/nitrofuran/config.php), заменив в нём парамерты
				соединения с БД на указанные. Он это сможет сделать только в том случае,
				если у него хватит на это прав.
			</p>
		<? endif; ?>
		<form method="post" action="install.php?stage=1">
			<table>
				<tr>
					<td>
						<input type="text" id="mysql_host" name="mysql_host" value="<?= isset($mysql_host) ? $mysql_host : 'localhost' ?>">
					</td>
					<td>
						<label for="mysql_host">Хост</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" id="mysql_user" name="mysql_user" value="<?= isset($mysql_user) ? $mysql_user : 'nitrofuran' ?>">
					</td>
					<td>
						<label for="mysql_user">Пользователь</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" id="mysql_password" name="mysql_password" value="<?= isset($mysql_password) ? $mysql_password : '' ?>">
					</td>
					<td>
						<label for="mysql_password">Пароль (имейте в виду, вводится без экранирования символов)</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" id="mysql_database" name="mysql_database" value="<?= isset($mysql_database) ? $mysql_database : 'nitrofuran' ?>">
					</td>
					<td>
						<label for="mysql_database">Название базы данных</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" id="create_user" name="create_user"<?= $create_user ? ' checked' : '' ?>>
					</td>
					<td>
						<label for="create_user">Создать указанного пользователя (требуется пароль на рута)</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="checkbox" id="create_database" name="create_database"<?= $create_database ? ' checked' : '' ?>>
					</td>
					<td>
						<label for="create_database">Создать указанную базу данных (требуется пароль на рута)</label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="text" id="rootpwd" name="rootpwd" value="<?= isset($rootpwd) ? $rootpwd : '' ?>">
					</td>
					<td>
						<label for="rootpwd">Пароль пользователя root в базе данных (имейте в виду, вводится без экранирования символов)</label>
					</td>
				</tr>
				<tr>
					<td></td>
					<td>
						<input type="submit" value="Зафигарить">
					</td>
				</tr>
			</table>
		</form>
	</div>
</body>
</html>