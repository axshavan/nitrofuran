<table class="optable main" cellspacing="0">
	<tr>
		<th>Приход</th>
		<th>Расход</th>
		<th>Дата и время</th>
		<th>Счёт</th>
		<th>Тип операции</th>
		<th>Комментарий</th>
		<th colspan="2"></th>
	</tr>
	<?
	$prevdate   = false;
	$daysum_inc = array();
	$daysum_exp = array();
	$daysum     = array();
	foreach($_operations as $_op)
	{
		$bOdd = !$bOdd;
		if(date('Yz', $_op['backtime']) != $prevdate)
		{
			// другая дата, надо вставить сепаратор
			if($prevdate !== false)
			{
				?><tr class="daysum"><td class="inc"><?
				foreach($daysum_inc as $k => $v)
				{
					echo $v.'&nbsp;'.$k.'<br>';
				}
				?></td><td class="exp"><?
				foreach($daysum_exp as $k => $v)
				{
					echo $v.'&nbsp;'.$k.'<br>';
				}
				?></td><td colspan="6">Итого: <?
				foreach($daysum as $k => $v)
				{
					echo $v.'&nbsp;'.$k.'<br>';
				}
				?></td></tr><?
			}
			?><tr><td class="dayhead" colspan="8"><?= rudate('d M Y', $_op['backtime']) ?></td></tr><?
			$prevdate   = date('Yz', $_op['backtime']);
			$daysum_inc = array();
			$daysum_exp = array();
			$daysum     = array();
		}
		if($_op['income'])
		{
			$daysum_inc[$_op['currency_symbol']] += $_op['amount'];
		}
		else
		{
			$daysum_exp[$_op['currency_symbol']] += $_op['amount'];
		}
		$daysum[$_op['currency_symbol']] += $_op['amount'] * ($_op['income'] ? 1 : -1 );
		?>
		<tr class="<?= $_op['income'] ? 'inc' : 'exp' ?><?= $bOdd ? '_odd' : '' ?>">
			<td><?= $_op['income'] ? ($_op['amount'].'&nbsp;'.$_op['currency_symbol']) : '' ?></td>
			<td><?= !$_op['income'] ? ($_op['amount'].'&nbsp;'.$_op['currency_symbol']) : '' ?></td>
			<td><?= rudate('d M Y H:i', $_op['time']) ?></td>
			<td><?= $_op['account'] ?></td>
			<td><?= $_op['optype'] ?></td>
			<td><?= $_op['comment'] ?></td>
			<td>
				<img
					class="button"
					src="<?= HTTP_ROOT ?>/i/kassa/del.gif"
					alt="Удалить"
					title="Удалить"
					onclick="
						if(confirm('Что, правда удалить?'))
						{
							document.location = '/kassa/delete/?id=<?= $_op['id'] ?>'
						}
					">
			</td>
			<td>
				<img
					class="button"
					src="<?= HTTP_ROOT ?>/i/kassa/edit.gif"
					alt="Редактировать"
					title="Редактировать"
					onclick="startEditEvent(
						this,
						{
							id:       '<?= $_op['id'] ?>',
							amount:   '<?= $_op['amount'] ?>',
							optype:   '<?= $_op['optype_id'] ?>',
							comment:  '<?= htmlspecialchars($_op['comment']) ?>',
							currency: '<?= $_op['currency_id'] ?>',
							account:  '<?= $_op['account_id'] ?>',
							backtime: '<?= $_op['backtime'] ?>'
						})">
			</td>
		</tr>
	<? } ?>
	<tr class="daysum"><td class="inc"><?
	foreach($daysum_inc as $k => $v)
	{
		echo $v.'&nbsp;'.$k.'<br>';
	}
	?></td><td class="exp"><?
	foreach($daysum_exp as $k => $v)
	{
		echo $v.'&nbsp;'.$k.'<br>';
	}
	?></td><td colspan="6">Итого: <?
	foreach($daysum as $k => $v)
	{
		echo $v.'&nbsp;'.$k.'<br>';
	}
	?></td></tr>
</table>