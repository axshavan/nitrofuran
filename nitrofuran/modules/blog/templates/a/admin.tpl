<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/admin.css">
    <script type="text/javascript" src="<?= HTTP_ROOT ?>/js/admin.js"></script>
    <title>NF Blog Admin Page</title>
</head>
<body>

<!-- левая панель -->
<div class="admin_left_panel">
	<? foreach($_left_menu as $item): ?>
	    <div class="item <?= $item['active'] ? '' : 'in' ?>active" onclick="document.location='<?= $item['href'] ?>'">
	        <a href="<?= $item['href'] ?>"><?= h($item['name']) ?></a>
	    </div>
	<? endforeach; ?>
</div>
<!-- /левая панель -->

<!-- правая панель -->
<div class="admin_right_panel">
    <div class="module_menu">
		<? foreach($_module_menu as $item): ?>
	        <div class="item <?= $item['active'] ? '' : 'in' ?>active" onclick="document.location='?module=<?= $module ?>&page=<?= $item['page'] ?>'">
				<?= $item['active'] ? '' : '<a href="?module='.$module.'&page='.$item['page'].'">' ?><?= h($item['name']) ?><?= $item['active'] ? '' : '</a>' ?>
	        </div>
		<? endforeach; ?>
    </div>
	<? $this->IncludeTemplate($inner_template_name); ?>
</div>
<!-- /правая панель -->

</body>
</html>