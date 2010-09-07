<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<meta http-equiv="Content-Type: text/html; charset=UTF-8">
    <title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa.js"></script>
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.2.min.js"></script>
</head>
<body>
    <div id="calendar_container" class="container">
		<!-- фильтр начала периода -->
		<div class="year">
			<a href="<?= $href_filter_from_prevyear ?>">&lt;&lt;</a>
			<?= $filter_from_year ?>
			<a href="<?= $href_filter_from_nextyear ?>">&gt;&gt;</a>
		</div>
		<div class="month">
			<a href="<?= $href_filter_from_prevmonth ?>">&lt;</a>
			<?= $filter_from_month ?>
			<a href="<?= $href_filter_from_nextmonth ?>">&gt;</a>
		</div>
		<!-- календарик -->
		<div class="calendar">
			<div class="week-head">
				<span>Пн</span><span>Вт</span><span>Ср</span><span>Чт</span><span>Пт</span><span class="end">Сб</span><span class="end">Вс</span>
			</div>
			<? foreach($_filter_from_calendar as $_week): ?>
				<div class="week">
					<? foreach($_week as $_day): ?><span class="<?= $_day['class'] ?>">
							<?= $_day['href'] ? '<a href="'.$_day['href'].'">' : '' ?>
							<?= $_day['text'] ?>
							<?= $_day['href'] ? '</a>' : '' ?>
						</span><? endforeach; ?>
				</div>
			<? endforeach; ?>
		</div>
		<!-- /календарик -->
		<a class="reset" href="<?= string_request_replace('from', 0) ?>">сбросить</a>
		<!-- /фильтр начала периода -->
		
		<!-- фильтр конца периода -->
		<div class="year">
			<a href="<?= $href_filter_to_prevyear ?>">&lt;&lt;</a>
			<?= $filter_to_year ?>
			<a href="<?= $href_filter_to_nextyear ?>">&gt;&gt;</a>
		</div>
		<div class="month">
			<a href="<?= $href_filter_to_prevmonth ?>">&lt;</a>
			<?= $filter_to_month ?>
			<a href="<?= $href_filter_to_nextmonth ?>">&gt;</a>
		</div>
		<!-- календарик -->
		<div class="calendar">
			<div class="week-head">
				<span>Пн</span><span>Вт</span><span>Ср</span><span>Чт</span><span>Пт</span><span class="end">Сб</span><span class="end">Вс</span>
			</div>
			<? foreach($_filter_to_calendar as $_week): ?>
				<div class="week">
					<? foreach($_week as $_day): ?><span class="<?= $_day['class'] ?>">
							<?= $_day['href'] ? '<a href="'.$_day['href'].'">' : '' ?>
							<?= $_day['text'] ?>
							<?= $_day['href'] ? '</a>' : '' ?>
						</span><? endforeach; ?>
				</div>
			<? endforeach; ?>
		</div>
		<!-- /календарик -->
		<a class="reset" href="<?= string_request_replace('to', 0) ?>">сбросить</a>
		<!-- /фильтр конца периода -->
	</div>
	
	<div class="container" id="main_container">
		<a class="reset" href="<?= HTTP_ROOT ?>/kassa/">сбросить все фильтры</a>
		<div class="add-form" id="add_form">
			<form action="<?= HTTP_ROOT ?>/kassa/add/" method="post">
				<strong>Добавить запись</strong><br>
				<div class="optypegroups">
					<? foreach($_optypegroups as $_group): ?>
					<span onclick="onTypeGroupClick(this, '<?= $_group['id'] ?>')"><?= $_group['name'] ?></span>
					<? endforeach; ?>
				</div>
				<? foreach($_optypes as $k => $_group): ?>
					<div class="optypegroup" id="optypegroup<?= $k?>">
						<? foreach($_group as $_optype): ?>
							<span class="<?= $_optype['is_income'] ? 'inc' : 'exp' ?>" onclick="onTypeClick(this, '<?= $_optype['id'] ?>')"><?= $_optype['name'] ?></span>
						<? endforeach; ?>
					</div>
				<? endforeach; ?>
				<select name="currency" id="inp_currency">
					<? foreach($_currencies as $_c): ?>
						<option value="<?= $_c['id'] ?>"><?= $_c['symbol'].' '.$_c['name'] ?></option>
					<? endforeach; ?>
				</select> <label for="inp_currency">Валюта</label><br>
				<select name="account" id="inp_account">
					<? foreach($_accounts as $_a): ?>
						<option value="<?= $_a['id'] ?>"><?= $_a['name'] ?></option>
					<? endforeach; ?>
				</select> <label for="inp_account">Счёт</label><br>
				<input type="text" name="amount" id="inp_amount"> <label for="inp_amount">Сумма</label><br>
				<input type="text" name="comment" id="inp_comment"> <label for="inp_comment">Комментарий</label><br>
				<input type="hidden" name="optype" id="inp_optype">
				<input type="submit" onclick="return checkAddForm()" value="Добавить запись">
			</form>
		</div>
		
		<!-- фильтр по типам операций -->
		<div class="optable" id="optypefilter">
			<strong>Фильтр по типам операций</strong> (<a href="<?= string_request_replace('type', 0) ?>">сбросить фильтр</a>)<br>
			<div class="optypegroups">
				<? foreach($_optypegroups as $_group): ?>
				<span onclick="onTypeGroupClick2(this, '<?= $_group['id'] ?>')"><?= $_group['name'] ?></span>
				<? endforeach; ?>
			</div>
			<? foreach($_optypes as $k => $_group): ?>
				<div class="optypegroupf<?= $show_group == $k ? '' : ' hidden' ?>" id="optypegroupf<?= $k?>">
					<? foreach($_group as $_optype): ?>
						<span class="<?= $_optype['is_income'] ? 'inc' : 'exp' ?><?= $_optype['id'] == $filter_type ? ' selectedf' : '' ?>" onclick="document.location='<?= string_request_replace('type', $_optype['id'] ) ?>'"><?= $_optype['name'] ?></span>
					<? endforeach; ?>
				</div>
			<? endforeach; ?>
		</div>
		<!-- /фильтр по типам операций -->
		
		<!-- фильтр по счёту -->
		<div class="optable" id="accountfilter">
			<strong>Фильтр по счёту</strong> (<a href="<?= string_request_replace('account', 0) ?>">сбросить фильтр</a>)<br>
			<div>
				<? foreach($_accounts as $_a): ?>
					<span class="<?= $_a['id'] == $filter_account ? ' selectedf1' : '' ?>" onclick="document.location='<?= string_request_replace('account', $_a['id'] ) ?>'"><?= $_a['name'] ?></span>
				<? endforeach; ?>
			</div>
		</div>
		<!-- /фильтр по счёту -->
		
		<!-- таблица с операциями -->
		<div class="optable">
			<table class="optable" cellspacing="0">
				<tr>
					<th>Приход</th>
					<th>Расход</th>
					<th>Дата и время</th>
					<th>Счёт</th>
					<th>Тип операции</th>
					<th>Комментарий</th>
					<th></th>
				</tr>
				<?
				$prevdate   = false;
				$daysum_inc = array();
				$daysum_exp = array();
				$daysum     = array();
				foreach($_operations as $_op)
				{
					$bOdd = !$bOdd;
					if(date('N', $_op['time']) != $prevdate)
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
							?></td><td colspan="5">Итого: <?
							foreach($daysum as $k => $v)
							{
								echo $v.'&nbsp;'.$k.'<br>';
							}
							?></td></tr><?
						}
						?><tr><td class="dayhead" colspan="7"><?= rudate('d M Y', $_op['time']) ?></td></tr><?
						$prevdate   = date('N', $_op['time']);
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
								src="<?= HTTP_ROOT ?>/i/kassa/edit.gif"
								alt="Редактировать"
								title="Редактировать"
								onclick="startEditEvent(
									this,
									{
										id:       '<?= $_op['id'] ?>',
										amount:   '<?= $_op['amount']?>',
										optype:   '<?= $_op['optype_id']?>',
										comment:  '<?= $_op['comment']?>',
										currency: '<?= $_op['currency_id']?>',
										account:  '<?= $_op['account_id']?>'
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
				?></td><td colspan="5">Итого: <?
				foreach($daysum as $k => $v)
				{
					echo $v.'&nbsp;'.$k.'<br>';
				}
				?></td></tr>
			</table>
		</div>
		<!-- /таблица с операциями -->
		
		<!-- итого -->
		<div class="itogo">
			<strong>В кассе всего</strong><br>
			<? foreach($_sum_all as $currency => $amount): ?>
				<span class="<?= $amount > 0 ? 'inc' : 'exp' ?>"><?= $amount.'&nbsp;'.$currency ?></span><br>
			<? endforeach; ?>
			<strong>С выбранным фильтром</strong><br>
			<? foreach($_sum_filtered as $currency => $amount): ?>
				<span class="<?= $amount > 0 ? 'inc' : 'exp' ?>"><?= $amount.'&nbsp;'.$currency ?></span><br>
			<? endforeach; ?>
		</div>
		<!-- /итого -->
		
		<!-- ссылки -->
		<div class="linx">
			<strong>Ссылки</strong><br>
			<a href="/admin?module=kassa&page=1">настройки кассы</a><br>
			<a href="/kassa/stats/">статистика</a>
		</div>
		<!-- /ссылки -->
	</div>
	
	<!-- форма редактирования события -->
	<div id="event_edit_form">
		<form action="<?= HTTP_ROOT ?>/kassa/edit/" method="post">
			<span onclick="$('#event_edit_form').fadeOut(300);">отмена</span><br>
			<img src="<?= HTTP_ROOT ?>/i/kassa/event_edit_form_arrow.gif">
			<select name="optype" id="event_edit_form_optype">
			<?
			foreach($_optypes as $_group)
			{
				foreach($_group as $_optype)
				{
					echo '<option value="'.$_optype['id'].'">'.$_optype['name'].'</option>';
				}
			}
			?>
			</select> <label for="event_edit_form_optype">Тип операции</label><br>
			<select name="currency" id="event_edit_form_currency">
				<? foreach($_currencies as $_c): ?>
					<option value="<?= $_c['id'] ?>"><?= $_c['symbol'].' '.$_c['name'] ?></option>
				<? endforeach; ?>
			</select> <label for="event_edit_form_currency">Валюта</label><br>
			<select name="account" id="event_edit_form_account">
				<? foreach($_accounts as $_a): ?>
					<option value="<?= $_a['id'] ?>"><?= $_a['name'] ?></option>
				<? endforeach; ?>
			</select> <label for="event_edit_form_account">Счёт</label><br>
			<input type="text" name="amount" id="event_edit_form_amount"> <label for="event_edit_form_amount">Сумма</label><br>
			<input type="text" name="comment" id="event_edit_form_comment"> <label for="event_edit_form_comment">Комментарий</label><br>
			<input type="hidden" name="id" id="event_edit_form_hidden">
			<input type="submit" value="Сохранить">
		</form>
	</div>
	<!-- /форма редактирования события -->
</body>
</html>
