<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />
	<title>Reader</title>
	<script type="text/javascript" src="/js/jquery-1.4.3.min.js"></script>
	<script type="text/javascript">
		var curpath = '<?= $curpath ?>';
	</script>
    <script type="text/javascript" src="/js/reader.js"></script>
	<link rel="stylesheet" type="text/css" href="/css/reader.css" />
</head>
<body>
	<div class="header">
		<input type="button" value="Добавить подписку" onclick="$('#addsform').fadeIn(); $('#addsform input').focus();">
		<div id="editsform">
			<span id="editsform_href"></span>
			<input type="hidden" id="editsform_id" />
			<input type="text" id="editsform_name" />
			<select id="editsform_group"></select>
			<input type="button" value="Сохранить" onclick="saveSubscription()" />
		</div>
	</div>
	<div class="left">
		<div id="subscr"></div>
		<input class="addGroup" type="button" value="Добавить папку" onclick="$('#addgform').fadeIn(); $('#addgform input').focus();">
	</div>
    <div id="right"></div>
    <div class="footer"></div>

	<!-- формы -->
	<!-- форма добавления группы -->
	<div id="addgform" class="addform">
		Введите название папки для подписок и нажмите Enter (или Esc, если вы передумали)
		<input type="text" onkeypress="if(event.keyCode == 10 || event.keyCode == 13) addGFormSubmit(); else if(event.keyCode == 27) $('#addgform').fadeOut();">
	</div>
    <!-- /форма добавления группы -->

    <!-- форма добавления подписки -->
    <div id="addsform" class="addform">
	    Введите адрес новой подписки и нажмите Enter (или Esc, если вы передумали)
        <input type="text" onkeypress="if(event.keyCode == 10 || event.keyCode == 13) addSFormSubmit(); else if(event.keyCode == 27) $('#addsform').fadeOut();">
    </div>
    <!-- /форма добавления подписки -->
    <!-- /формы -->

	<!-- чёрная аяксовая занавеска -->
	<div id="curtain"></div>
    <!-- /чёрная аяксовая занавеска -->
</body>
</html>