<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa_240px.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa_240px.js"></script>
</head>
<body>
	<form action="/kassa/add/" method="post" onsubmit="return onFormSubmit();">
		<input type="hidden" name="optype" id="inp_optype"><?
		?><label for="inp_amount" class="half">Сумма</label><?
		?><input type="text" name="amount" id="inp_amount" class="half"><?
		for($i = 0; $i < 10; $i++)
		{
			?><input type="button" value="<?= $i ?>" class="quarter" onclick="document.getElementById('inp_amount').value+='<?= $i ?>';"><?
		}
		?><input type="button" value="," class="quarter" onclick="document.getElementById('inp_amount').value+=',';"><?
		?><input type="button" value="&larr;" class="quarter" onclick="var o=document.getElementById('inp_amount');if(o){o.value=o.value.substr(0,o.value.length-1)}"><?
		?><br><br><?
		?><label for="inp_currency" class="half">Валюта</label><?
		?><select name="currency" id="inp_currency" class="half"><?
			foreach($_currencies as $_c):
				?><option value="<?= $_c['id'] ?>"<?= $_c['default'] ? ' selected' : '' ?>><?= $_c['symbol'].' '.$_c['name'] ?></option><?
			endforeach;
		?></select><?
		?><label for="inp_account" class="half">Счёт</label><?
		?><select class="half" name="account" id="inp_account"><?
		foreach($_accounts as $_a): if($_a['show']): ?>
			?><option value="<?= $_a['id'] ?>"<?= $_a['default'] ? ' selected' : '' ?>><?= $_a['name'] ?></option><?
		endif; endforeach; ?>
		?></select><?
		?><label for="inp_comment" class="full">Комментарий</label><?
		?><input type="text" name="comment" id="inp_comment" class="full"><?
		?><label class="half" for="inp_opttypegroup">Группа типов</label><?
		?><label class="half">Тип операции</label><?
		?><select class="half" id="inp_opttypegroup" onchange="onGroupSelectChange(this.value)"><?
			?><option value="0"></option><?
		foreach($_optypegroups as $k => $_group):
			?><option value="<?= $k ?>"><?= $_group['name'] ?></option><?
		endforeach;
		?></select><?
		?><script type="text/javascript">
			maxTypeGroupId = '<?= $k ?>';
		</script><?
		?><span id="inp_optype0">Группа?</span><?
		foreach($_optypes as $k => $_group):
			?><select class="half optype" id="inp_optype<?= $k ?>" onchange="onTypeSelectChange(this.value)"><?
				foreach($_group as $_optype):
					?><option value="<?= $_optype['id'] ?>"><?= $_optype['name'] ?></span><?
				endforeach;
			?></select><?
		endforeach;
		?><br><br><br><input class="full" type="submit" value="Добавить">
	</form>
</body>
</html>