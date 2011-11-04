<p>Это форма добавления нового пользователя. Обязательны только поля login и email (логин и емейл,
соответственно). Пароль вводится без экранирования символов.</p>

<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? elseif($success_text): ?>
	<div class="success"><?= $success_text ?></div>
<? endif; ?>

<form action="/admin/?module=user&page=2" method="post">
	<table cellspacing="0" class="admin_table">
		<tr>
			<td>Логин</td>
			<td><input type="text" name="login" value=""></td>
		</tr>
		<tr>
			<td>Полное имя</td>
			<td><input type="text" name="full_name" value=""></td>
		</tr>
		<tr>
			<td>Email</td>
			<td><input type="text" name="email" value=""></td>
		</tr>
		<tr>
			<td>Пароль</td>
			<td><input type="text" name="password" value=""></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Создать пользователя"></td>
		</tr>
	</table>
</form>