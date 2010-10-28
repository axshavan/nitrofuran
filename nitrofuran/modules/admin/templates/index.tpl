<?

// названия, типа языковой переменной
$_params_types = array(
	'text'      => 'текст',
	'textarea'  => 'большой текст',
	'textarray' => 'массив'
);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/admin.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/admin.js"></script>
	<title>NF Admin Page</title>
</head>
<body>
	
	<!-- левая панель -->
	<div class="admin_left_panel">
		<? foreach($_left_menu as $item): ?>
		<div class="item <?= $item['active'] ? '' : 'in' ?>active">
			<a href="?module=<?= $item['module'] ?>"><?= h($item['name']) ?></a>
		</div>
		<? endforeach; ?>
	</div>
	<!-- /левая панель -->
	
	<!-- правая панель -->
	<div class="admin_right_panel">
		<div class="module_menu">
			<? foreach($_module_menu as $item): ?>
				<div class="item <?= $item['active'] ? '' : 'in' ?>active">
					<?= $item['active'] ? '' : '<a href="?module='.$module.'&page='.$item['page'].'">' ?><?= h($item['name']) ?><?= $item['active'] ? '' : '</a>' ?>
				</div>
			<? endforeach; ?>
		</div>
		<? if(is_array($_options)): ?>
		<form method="post">
		<table class="admin_table" cellspacing="0">
			<? foreach($_options as $_option): ?>
			<tr>
				<td>
					<?= strlen($_option['display_name']) ? h($_option['display_name']) : $_option['name'] ?><br>
					<span class="comment"><?= $_option['name'] ?>:
					<?= $_params_types[$_option['type']] ?>
					</span>
				</td>
				<td>
					<?
					// обработка параметров в зависимости от их типов
					switch($_option['type'])
					{
						// массив
						case 'textarray':
						{
							$_option['value'] = unserialize($_option['value']);
							$option_sub_id = 0;
							?><input type="hidden" name="o[<?= $_option['name'] ?>]" id="o_<?= $_option['name'] ?>" value=""><?
							foreach($_option['value'] as $k => $v)
							{
								?><input type="text" size="2" maxsize="5" id="o_<?= $_option['name']?>_key_<?= $option_sub_id ?>" name="o[<?= $_option['name']?>_key_<?= $option_sub_id ?>]" value="<?= h($k) ?>">&nbsp;<input type="text" size="20" id="o_<?= $_option['name'] ?>_value_<?= $option_sub_id ?>" name="o[<?= $_option['name'] ?>_value_<?= $option_sub_id ?>]" value="<?= h($v) ?>"><br><?
								$option_sub_id++;
							}
							?><span class="comment">Добавить ключ (не обязательно) и значение:</span><br><input type="text" size="2" maxsize="5" id="o_<?= $_option['name']?>_key_<?= $option_sub_id ?>" name="o[<?= $_option['name']?>_key_<?= $option_sub_id ?>]" value="">&nbsp;<input type="text" size="20" id="o_<?= $_option['name'] ?>_value_<?= $option_sub_id ?>" name="o[<?= $_option['name'] ?>_value_<?= $option_sub_id ?>]" value=""><?
							break;
						}
						// текстареа
						case 'textarea':
						{
							$_option['value'] = htmlspecialchars($_option['value']);
							?><textarea id="o_<?= $_option['name'] ?>" name="o[<?= $_option['name'] ?>]" rows="5" cols="25"><?= h($_option['value']) ?></textarea><?
							break;
						}
						// всё остальное
						case 'text':
						default:
						{
							$_option['value'] = htmlspecialchars($_option['value']);
							?><input type="text" id="o_<?= $_option['name'] ?>" name="o[<?= $_option['name'] ?>]" size="25" value="<?= h($_option['value']) ?>"><?
						}
					}
					?>
				</td>
			</tr>
			<? endforeach; ?>
			<tr>
				<td>Добавить новый параметр модуля</td>
				<td>
					<span class="comment">Название</span><br>
					<input type="text" size="25" id="new_param_name" name="new_param_name"><br>
					<span class="comment">Название по-русски</span><br>
					<input type="text" size="25" id="new_param_display_name" name="new_param_display_name"><br>
					<span class="comment">Тип</span><br>
					<select id="new_param_type" name="new_param_type">
						<option value=""></option>
						<? foreach($_params_types as $k => $v): ?>
							<option value="<?= $k ?>"><?= $v ?></option>
						<? endforeach; ?>
					</select>
				</td>
			</tr>
			<tr>
				<td><input type="reset" value="Вернуть как было"></td>
				<td><input type="submit" value="Сохранить"></td>
			</tr>
		</table>
		</form>
		<? else: ?>
		<? $this->IncludeTemplate($inner_template_name); ?>
		<? endif; ?>
	</div>
	<!-- /правая панель -->
</body>
</html>