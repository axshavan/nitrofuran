<?

function te_draw_category_recursively($_cat)
{
	//trace($_cat);
	?>
	<div class="category" id="cat_<?= $_cat['id'] ?>">
		
	</div>
	<?
}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type: text/html; charset=UTF-8">
    <title>Хранилище паролей</title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kokol.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.3.min.js"></script>
</head>
<body>
	<?
	//trace($_data);
	foreach($_data['children'] as &$_cat)
	{
		te_draw_category_recursively($_cat);
	}
	?>
</body>
</html>