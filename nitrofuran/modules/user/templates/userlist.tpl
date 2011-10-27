<table cellspacing="0" class="admin_table">
	<tr>
		<th>id</th>
		<th>Логин</th>
		<th>Полное имя</th>
		<th>Email</th>
		<th>Задать новый пароль</th>
		<th></th>
	</tr>
	<? foreach($_users as $user): ?>
	<form action="?module=user&page=1" method="post" id="user<?= $user['id'] ?>form">
		<input type="hidden" name="id" value="<?= $user['id'] ?>">
		<tr>
			
				<td><?= $user['id'] ?></td>
				<td><input type="text" name="login" value="<?= htmlspecialchars($user['login']) ?>"></td>
				<td><input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>"></td>
				<td><input type="text" name="email" value="<?= htmlspecialchars($user['email']) ?>"></td>
				<td><input type="text" name="newpassword" value=""></td>
				<td><img src="/i/admin/ok.gif" onclick="ge('user<?= $user['id'] ?>form').submit();"></td>
			
		</tr>
	</form>
	<? endforeach; ?>
</table>


<?/*<p>Этот модуль задумывался как мощное устройство управления пользователями, но
в условиях текущей её востребованности служит пока для задания логина и пароля
пользователя с номером 1, который считается по умолчанию суперпользователем
и имеет доступ ко всем внутренностям движка, в том числе и к этой странице.</p>

<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? elseif($success_text): ?>
	<div class="success"><?= $success_text ?></div>
<? endif; ?>


<form action="?module=user&page=1" method="post">
	<table cellspacing="0" class="admin_table">
		<tr>
			<td><label for="user_1_login">Логин</label></td>
			<td><input type="text" id="user_1_login" name="user_1_login" value="<?= $_users[1]['login'] ?>"></td>
		</tr>
		<tr>
			<td>
				<label for="user_1_password">Новый пароль</label><br>
				<span class="comment">Вводится без экранирования символов</span>
			</td>
			<td><input type="text" id="user_1_password" name="user_1_password" value=""></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td><input type="submit" value="Готово"></td>
		</tr>
	</table>
</form>*/?>