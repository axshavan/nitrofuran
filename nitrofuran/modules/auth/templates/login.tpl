<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf8">
	<title>Добро пожаловать</title>
</head>
<body>
	<?= $error ?>
	<fieldset>
		<legend>Форма авторизации</legend>
		<form action="/login/" method="post">
			<table>
				<tr>
					<td><label for="login">Логин</label></td>
					<td><input type="text" name="login" id="login" value="<?= $login ?>"></td>
				</tr>
				<tr>
					<td><label for="password">Пароль</label></td>
					<td><input type="password" name="password" id="password"></td>
				</tr>
				<tr>
					<td><label for="remember">Запомнить</label></td>
					<td><input type="checkbox" name="remember" id="remember" value="1"<?= $remember ? ' checked' : ''?>></td>
				</tr>
				<tr>
					<td><label for="bind2ip">Привязать к ip</label></td>
					<td><input type="checkbox" name="bind2ip" id="bind2ip" value="1"<?= $remember ? ' checked' : ''?>></td>
				</tr>
				<tr>
					<td><input type="submit" value="Войти"></td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</form>
	</fieldset>
</body>
</html>