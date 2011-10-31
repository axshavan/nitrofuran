<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? elseif($success_text): ?>
	<div class="success"><?= $success_text ?></div>
<? endif; ?>

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