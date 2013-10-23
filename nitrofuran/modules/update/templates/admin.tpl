<? if($error_text): ?>
	<div class="error"><?= $error_text ?></div>
<? elseif($success_text): ?>
	<div class="success"><?= $success_text ?></div>
<? endif; ?>

<?
switch($_REQUEST['page'])
{
	case 3:
	{
		?>
	    <table class="admin_table" cellspacing="0">
	        <tr>
	            <td>Установленная локальная версия таблиц</td>
	            <td><?= $version ?></td>
	        </tr>
	        <tr>
	            <td>Дата последнего обновления локальной версии</td>
	            <td><?= $last_update ?></td>
	        </tr>
	        <tr>
	            <td>Доступная локальная версия таблиц</td>
	            <td><?= $available_version ?></td>
	        </tr>
	    </table>
		<? if($version < $available_version): ?>
	    <input type="button" value="Обновить" onclick="document.location='/admin/?module=update&page=3&proceed'">
		<? endif;
		break;
	}
	case 2:
	{
		if(isset($_REQUEST['download']))
		{
			echo '<pre>'.$page_content.'</pre>';
			?>
			Пожалуйста, убедитесь в том, что архив распакован, сделана резервная копия,
			после чего можно<br /><input type="button" value="Обновить исходники" onclick="document.location='/admin/?module=update&page=2&overwrite'">
			или <input type="button" value="Очистить временную папку" onclick="document.location='/admin/?module=update&page=2&cleartmp'">
			<?
		}
		elseif(isset($_REQUEST['cleartmp']) || isset($_REQUEST['overwrite']))
		{
			echo '<pre>'.$page_content.'</pre>';
		}
		else
		{
			?>
			<input type="button" value="Скачать последнюю версию" onclick="document.location='/admin/?module=update&page=2&download'">
			<?
		}
		break;
	}
	case 1:
	default:
	{
		?>
		<table class="admin_table" cellspacing="0">
			<tr>
				<td>Установленная версия таблиц</td>
				<td><?= $version ?></td>
			</tr>
			<tr>
				<td>Дата последнего обновления</td>
				<td><?= $last_update ?></td>
			</tr>
			<tr>
				<td>Доступная версия таблиц</td>
				<td><?= $available_version ?></td>
			</tr>
		</table>
		<? if($version < $available_version): ?>
			<input type="button" value="Обновить" onclick="document.location='/admin/?module=update&page=1&proceed'">
		<? endif;
		break;
	}
}