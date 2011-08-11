<link rel="stylesheet" type="text/css" href="/css/static.css">
<script type="text/javascript" src="/js/admin.js"></script>
<script type="text/javascript" src="/js/static.js"></script>

<form action="/admin?module=static&page=3" method="post">
	<input type="hidden" name="pageid" value="<?= $_page['id'] ?>">
	<input type="submit" value="Готово"><br>
	<textarea onkeypress="onTextareaKeyPress(this, event.keyCode);" name="content" id="content" class="full"><?= htmlspecialchars(stripcslashes($_page['content'])) ?></textarea><br>
	<input type="submit" value="Готово" onfocus="onSubmitFocus()">
</form>