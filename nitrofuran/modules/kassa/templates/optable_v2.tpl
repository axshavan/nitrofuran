<link rel="stylesheet" type="text/css" href="/css/kassa_v2.css" />
<div class="optable_v2">
	<?php
	
	/**
	 * Отрисовать один день
	 * @param array &$_day          данные за день
	 * @param int   $current_date_t таймстамп отрисовываемого дня
	 * @param array $_currencies    список валют
	 * @param array $_accounts      аккаунты
	 */
	function optablev2_draw_day(&$_day, $current_date_t, $_currencies, $_accounts)
	{
		?>
		<div class="day_head">
			<span class="command" onclick="$('#<?= $current_date_t ?>_daybody').slideToggle();">[+/-]</span>
			<?
				echo '<span class="title">'.rudate('d M Y', $current_date_t).'</span>';
				foreach($_day['currencies'] as $cur_id => &$amount)
				{
					if($amount)
					{
						if($amount > 0)
						{
							$amount_sign = 1;
							echo ' получено <span class="ind">';
						}
						else
						{
							$amount_sign = -1;
							echo ' потрачено <span class="dec">';
						}
						echo round($amount * $amount_sign, 2).' '.$_currencies[$cur_id]['symbol'].'</span> ';
					}
				}
			?>
		</div>
		<div class="day_body<?= $current_date_t < time() - 86400 * 7 ? ' hidden' : '' ?>" id="<?= $current_date_t ?>_daybody">
			<? foreach($_day['accounts'] as $acc_id => &$_accdata): ?>
				<div class="account_head">
					<span class="command" onclick="$('#<?= $current_date_t.'_'.$acc_id ?>_accbody').slideToggle();">[+/-]</span>
					<?
						echo '<span class="title">'.h($_accounts[$acc_id]['name']).'</span>';
						foreach($_accdata['currencies'] as $cur_id => &$amount)
						{
							if($amount )
							{
								if($amount > 0)
								{
									$amount_sign = 1;
									echo ' приход <span class="ind">';
								}
								else
								{
									$amount_sign = -1;
									echo ' расход <span class="dec">';
								}
								echo round($amount * $amount_sign, 2).' '.$_currencies[$cur_id]['symbol'].'</span> ';
							}
						}
					?>
				</div>
				<div class="account_body<?= $current_date_t < time() - 86400 * 2 ? ' hidden' : '' ?>" id="<?= $current_date_t.'_'.$acc_id ?>_accbody">
					<? foreach($_accdata['operations'] as $cur_id => $_curdata): ?>
						<div class="currency_head">
							<span class="title"><?= $_currencies[$cur_id]['symbol'] ?></span>
							<?= h($_currencies[$cur_id]['name']) ?>
						</div>
						<div class="currency_body">
							<table class="optable" cellspacing="0">
								<? foreach($_curdata as &$o): $bOdd = !$bOdd; ?>
									<tr class="<?= $o['income'] ? 'inc' : 'exp' ?><?= $bOdd ? '_odd' : '' ?>">
										<td class="td1">
											<?= ($o['income'] ? '+' : '-').$o['amount'].'&nbsp;'.$_currencies[$cur_id]['symbol'] ?>
										</td>
										<td class="td2">
											<?= h($o['optype']) ?>
										</td>
										<td class="td3">
											<?= h($o['comment']) ?>
										</td>
										<td class="td4">
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
												" />
											<img
												class="button"
												src="<?= HTTP_ROOT ?>/i/kassa/edit.gif"
												alt="Редактировать"
												title="Редактировать"
												onclick="startEditEvent(
													this,
													{
														id:       '<?= $o['id'] ?>',
														amount:   '<?= $o['amount'] ?>',
														optype:   '<?= $o['optype_id'] ?>',
														comment:  '<?= htmlspecialchars($o['comment']) ?>',
														currency: '<?= $o['currency_id'] ?>',
														account:  '<?= $o['account_id'] ?>',
														backtime: '<?= $o['backtime'] ?>'
													})
												" />
										</td>
									</tr>
								<? endforeach; ?>
							</table>
						</div>
					<? endforeach; ?>
				</div>
			<? endforeach; ?>
		</div>
		<?
	}
	?>
	<span class="command" onclick="$('[id$=\'body\']').slideDown();">Развернуть всё</span>&nbsp;
	<span class="command" onclick="$('[id$=\'body\']').slideUp();">Свернуть всё</span>
	<?
	$current_date     = '';
	$current_account  = 0;
	$current_currency = 0;
	$current_date_t   = '';
	$_day             = array();
	$sizeofoperations = sizeof($_operations);
	$op_counter       = 0;
	foreach($_operations as &$_op)
	{
		$_op['v2date']   = date('Yz', $_op['backtime']);
		$_op['v2amount'] = ($_op['income'] ? 1 : -1) * $_op['amount'];
		if($current_date != $_op['v2date'] || $sizeofoperations == $op_counter)
		{
			if($_day)
			{
				optablev2_draw_day($_day, $current_date_t, $_currencies, $_accounts);
			}
			$current_date     = $_op['v2date'];
			$current_account  = $_op['account'];
			$current_currency = $_op['currency'];
			$current_date_t   = $_op['backtime'];
			$_day             = array();
		}
		if($_op['amount'])
		{
			$_day['currencies'][$_op['currency_id']] += $_op['v2amount'];
			$_day['accounts'][$_op['account_id']]['currencies'][$_op['currency_id']] += $_op['v2amount'];
			$_day['accounts'][$_op['account_id']]['operations'][$_op['currency_id']][] = $_op;
			// если это последняя операция, и она всего одна за свой день
			if($op_counter == $sizeofoperations - 1)
			{
				optablev2_draw_day($_day, $current_date_t, $_currencies, $_accounts);
			}
		}
		$op_counter++;
	}
	?>
</div>