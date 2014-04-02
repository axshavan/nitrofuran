<!DOCTYPE html>
<html>
<head>
    <title>Марки</title>
    <link rel="stylesheet" type="text/css" href="/css/stamps.css" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
	<form action="/stamps/import" method="post" enctype="multipart/form-data">
		Загрузить файл *.ods в специальном формате<br />
		<input type="hidden" name="import" value="1"/>
		<input type="file" name="file" /> <input type="submit" value="Готово"/>
	</form>
</body>
</html>