<?

function te_draw_category_recursively(&$_cat)
{
	?>
	<div class="category" id="cat_<?= $_cat['id'] ?>">
		<h3><?= $_cat['data']['name'] ?></h3>
		<?
		if(sizeof($_cat['children']))
		{
			?>
			<span class="command" onclick="$('#subcat_<?= $_cat['id'] ?>').slideToggle(100);">Подразделы</span>
			<div class="subcategories" id="subcat_<?= $_cat['id'] ?>">
			<?
			foreach($_cat['children'] as &$_subcat)
			{
				te_draw_category_recursively($_subcat);
			}
			?></div><br><?
		}
		if(sizeof($_cat['data']['passwords']))
		{
			?>
			<!--<span class="command" onclick="$('#pwd_<?= $_cat['id'] ?>').slideToggle(100);">Пароли</span>-->
			<div class="passwords" id="pwd_<?= $_cat['id'] ?>">
				<table cellspacing="0">
					<? foreach($_cat['data']['passwords'] as $_password): ?>
						<tr>
							<form action="<?= HTTP_ROOT ?>/kokol/edit/" method="post" id="form_<?= $_password['id'] ?>">
								<td>
									<input
										type="text"
										id="form_<?= $_password['id'] ?>_resource"
										name="updpwdr"
										value="<?= $_password['resource'] ?>">
								</td>
								<td>
									<input
										type="text"
										id="form_<?= $_password['id'] ?>_login"
										name="updpwdl"
										value="<?= $_password['login'] ?>"
										onclick="this.select()">
								</td>
								<td>
									<input
										type="text"
										id="form_<?= $_password['id'] ?>_password"
										name="updpwdp"
										value="<?= $_password['password'] ?>"
										onclick="this.select()">
								</td>
								<td><img
									src="/i/kokol/save.gif"
									onclick="ge('form_<?= $_password['id']?>').submit();"></td>
								<td><img
									src="/i/kokol/del.gif"
									onclick="if(confirm('Точно удалить?')) document.location='<?= HTTP_ROOT ?>/kokol/edit/?delpwd=<?= $_password['id'] ?>'"></td>
								<input type="hidden" name="updpwdid" value="<?= $_password['id'] ?>">
							</form>
						</tr>
					<? endforeach; ?>
				</table>
			</div>
			<?
		}
		?>
		<span class="command" onclick="$('#div_add<?= $_cat['id'] ?>').slideToggle();">[+]</span>
		<div class="add" id="div_add<?= $_cat['id'] ?>">
			<div class="addcat">
				<input type="text" id="addcatname<?= $_cat['id'] ?>">
				<input type="button" value="Добавить категорию" onclick="document.location='<?= HTTP_ROOT ?>/kokol/edit/?addcat=' + ge('addcatname<?= $_cat['id'] ?>').value + '&root_cat=<?= $_cat['id'] ?>'">
			</div>
			<div class="addcat">
				<form action="<?= HTTP_ROOT ?>/kokol/edit/" method="post">
					<input type="text" name="addpwdr" id="addpwdr<?= $_cat['id'] ?>"> <label for="addpwdr<?= $_cat['id'] ?>">Ресурс</label><br>
					<input type="text" name="addpwdl" id="addpwdl<?= $_cat['id'] ?>"> <label for="addpwdl<?= $_cat['id'] ?>">Логин</label><br>
					<input type="text" name="addpwdp" id="addpwdp<?= $_cat['id'] ?>"> <label for="addpwdp<?= $_cat['id'] ?>">Пароль</label><br>
					<input type="hidden" name="root_cat" value="<?= $_cat['id'] ?>">
					<input type="submit" value="Добавить пароль">
				</form>
			</div>
		</div>
	</div>
	<?
}

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type: text/html; charset=UTF-8">
    <title>Хранилище паролей</title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kokol.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.3.min.js"></script>
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kokol.js"></script>
</head>
<body>
	<?
	foreach($_data['children'] as &$_cat)
	{
		te_draw_category_recursively($_cat);
	}
	?>
	<div class="addcat">
		<input type="text" id="addcatname0">
		<input type="button" value="Добавить категорию" onclick="document.location='<?= HTTP_ROOT ?>/kokol/edit/?addcat=' + ge('addcatname0').value + '&root_cat=0'">
	</div>
	<div class="whykokol">
		<span class="command" onclick="$('#whykokol').slideDown();">Что ещё за &laquo;коколь&raquo;?</span>
		<div id="whykokol">
			&mdash; Скажи пароль?<br>
			&mdash; На горшке сидел король.<br>
			Моя младшая дочь в возрасте двух лет, вылушав этот диалог, на вопрос
			о том, как мне назвать хранилище паролей, ответила (видимо, услышав
			знакомое слово &laquo;пароль&raquo;): &laquo;коколь&raquo;
			(&laquo;король&raquo;, значит). Так я и назвал.
		</div>
	</div>
</body>
</html>