<? header("Content-Type: text/html; charset=utf8"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
	<title><?= $title ?></title>
	<link rel="stylesheet" type="text/css" href="<?= HTTP_ROOT ?>/css/kassa.css">
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/jquery-1.4.3.min.js"></script>
	<script type="text/javascript" src="<?= HTTP_ROOT ?>/js/kassa.js"></script>
</head>
<body>
	
	<!-- календарики -->
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
	<!-- /календарики -->
	
	<!-- основная часть -->
	<div class="container" id="main_container">
		<a class="reset" href="<?= HTTP_ROOT ?>/kassa/">сбросить все фильтры</a>
		<!-- форма добавления -->
		<div class="add-form" id="add_form">
			<form action="<?= HTTP_ROOT ?>/kassa/add/" method="post">
				<strong>Добавить запись</strong>
				<? foreach($_frequent_types as $_type): ?>
					<span class="command frequent" title="<?= $_type['gname'] ?>/<?= $_type['tname'] ?>" onclick="onFrequentTypeClick(<?= $_type['tid'] ?>, <?= $_type['gid'] ?>)"><?= $_type['tname'] ?></span>
				<? endforeach; ?>
				<br>
				<div class="optypegroups">
					<? foreach($_optypegroups as $_group): ?>
					<span id="span_group_<?= $_group['id'] ?>" onclick="onTypeGroupClick(this, '<?= $_group['id'] ?>')"><?= $_group['name'] ?></span>
					<? endforeach; ?>
				</div>
				<? foreach($_optypes as $k => $_group): ?>
					<div class="optypegroup" id="optypegroup<?= $k?>">
						<? foreach($_group as $_optype): ?>
							<span id="span_type_<?= $_optype['id'] ?>" class="<?= $_optype['is_income'] ? 'inc' : 'exp' ?>" onclick="onTypeClick(this, '<?= $_optype['id'] ?>')"><?= $_optype['name'] ?></span>
						<? endforeach; ?>
					</div>
				<? endforeach; ?>
				<select name="currency" id="inp_currency">
					<? foreach($_currencies as $_c): ?>
						<option value="<?= $_c['id'] ?>"<?= $_c['default'] ? ' selected' : '' ?>><?= $_c['symbol'].' '.$_c['name'] ?></option>
					<? endforeach; ?>
				</select> <label for="inp_currency">Валюта</label><br>
				<select name="account" id="inp_account">
					<? foreach($_accounts as $_a): ?>
						<option value="<?= $_a['id'] ?>"<?= $_a['default'] ? ' selected' : '' ?>><?= $_a['name'] ?></option>
					<? endforeach; ?>
				</select> <label for="inp_account">Счёт</label><br>
				<input type="text" name="amount" id="inp_amount"> <label for="inp_amount">Сумма</label> <span class="command" onclick="calc.show(ge('inp_amount'))">Калькулятор</span><br>
				<input type="text" name="comment" id="inp_comment" onkeyup="onCommentKeyUp(event)"> <label for="inp_comment">Комментарий</label><br>
				<div id="div_comment_tip">
					<div class="close">
						<span onclick="$('#div_comment_tip').slideUp(300);">[ X ]</span><br>
					</div>
					<div id="div_comment_tip_content">
					</div>
				</div>
				<input type="hidden" name="optype" id="inp_optype">
				<input type="submit" onclick="return checkAddForm()" value="Добавить запись">
				<span class="command" onclick="$('#backtimediv').slideToggle(); ge('backyear_select').value=''; ge('backmonth_select').value=''; ge('backday_select').value=''; ">задним числом</span>
				<div class="hidden" id="backtimediv">
					<select id="backyear_select" name="backyear">
						<option value=""></option>
						<? for($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
							<option value="<?= $y ?>"><?= $y ?></option>
						<? endfor; ?>
					</select>
					<select id="backmonth_select" name="backmonth">
						<option value=""></option>
						<? for($m = 1; $m <= 12; $m++): ?>
							<option value="<?= $m ?>"><?= $m ?></option>
						<? endfor; ?>
					</select>
					<select id="backday_select" name="backday">
						<option value=""></option>
						<? for($d = 1; $d <= 31; $d++): ?>
							<option value="<?= $d ?>"><?= $d ?></option>
						<? endfor; ?>
					</select>
				</div>
			</form>
		</div>
		<!-- /форма добавления -->
		
		<!-- всякое -->
		<span class="command" onclick="$('#some').slideToggle(300);">Всякое [+/-]</span>
		<div id="some"<?= sizeof($_debtor_operations) ? 'style="display: block;"' : '' ?>>
		
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
			
			<!-- долги -->
			<div class="optable" id="debtors">
				<span class="credit1">Человек должен вам</span> <span class="credit2">Вы ему должны</span><br>
				<table class="optable" cellspacing="0">
					<? foreach($_debtors as &$_debtor): $bOdd = !$bOdd; ?>
						<tr class="<?= $_debtor['amount'] < 0 ? 'exp' : 'inc' ?><?= $bOdd ? '_odd' : '' ?>">
							<td><a href="?debtor=<?= $_debtor['id'] ?>"><?= $_debtor['name'] ?></a></td>
							<td>
								<img
									class="button"
									src="<?= HTTP_ROOT ?>/i/kassa/add.gif"
									alt="Добавить операцию"
									title="Добавить операцию"
									onclick="showDebtorForm(this, '<?= $_debtor['id'] ?>')"
								>
							</td>
							<td><?= $_debtor['amount'] ?>&nbsp;<?= $_debtor['symbol'] ?></td>
						</tr>
					<? endforeach; ?>
				</table>
				<a href="/admin/?module=kassa&page=3">Управление должниками &raquo;</a>
				<? if(sizeof($_debtor_operations)): ?>
					<br><br>
					<table class="optable" cellspacing="0">
						<? foreach($_debtor_operations as $_op): $bOdd = !$bOdd; ?>
							<tr class="<?= $_op['amount'] > 0 ? 'inc' : 'exp' ?><?= $bOdd ? '_odd' : '' ?>">
								<td><?= $_op['date'] ? date('Y-m-d H:i', $_op['date']) : '' ?></td>
								<td><?= $_op['amount'] ?>&nbsp;<?= $_currencies[$_op['currency_id']]['symbol'] ?></td>
							</td>
						<? endforeach; ?>
					</table>
				<? endif; ?>
			</div>
			<!-- /долги -->
		</div>
		<!-- /всякое -->
		
		<div class="optable">
			<!-- таблица с холдами -->
			<table class="optable holdtable" cellspacing="0">
				<tr>
					<th>Отложено</th>
					<th>Счёт&nbsp;/ Тип операции</th>
					<th><img src="/i/kassa/add.gif" class="button" onclick="showHoldForm(this, false)"></th>
				</tr>
				<? foreach($holds as $h): $bOdd = !$bOdd; ?>
					<tr class="<?= $bOdd ? 'odd' : 'notodd' ?>">
						<td title="<?= htmlspecialchars($h['comment']) ?>"><span id="hold<?= $h['id'] ?>_sum"><?= (float)$h['sum'] ?></sum>&nbsp;<?= $_currencies[$h['currency_id']]['symbol'] ?></td>
						<td title="<?= htmlspecialchars($h['comment']) ?>"><?= $_accounts[$h['account_id']]['name'] ?>&nbsp;/ <?= $_optypes_by_id[$h['operation_type_id']]['name'] ?></td>
						<td>
							<img src="/i/kassa/edit.gif"
								class="button"
								onclick="showHoldForm(this, '<?= $h['id'] ?>')">
							<a onclick="if(!confirm('Правда удалить?')) return false;" href="/kassa/hold/?del=<?= $h['id'] ?>"><img src="/i/kassa/del.gif"></a>
						</td>
					</tr>
					<input type="hidden" id="hold<?= $h['id'] ?>_cur" value="<?= $h['currency_id'] ?>">
					<input type="hidden" id="hold<?= $h['id'] ?>_acc" value="<?= $h['account_id'] ?>">
					<input type="hidden" id="hold<?= $h['id'] ?>_optype" value="<?= $h['operation_type_id'] ?>">
					<input type="hidden" id="hold<?= $h['id'] ?>_comment" value="<?= htmlspecialchars($h['comment']) ?>">
				<? endforeach; ?>
			</table>
			<!-- /таблица с холдами -->
			
			<!-- таблица с операциями -->
			<table class="optable" cellspacing="0">
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
										amount:   '<?= $_op['amount']?>',
										optype:   '<?= $_op['optype_id']?>',
										comment:  '<?= $_op['comment']?>',
										currency: '<?= $_op['currency_id']?>',
										account:  '<?= $_op['account_id']?>',
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
			<!-- /таблица с операциями -->
		</div>
		
		<!-- итого -->
		<div class="itogo">
			<strong>В кассе всего</strong>
			<table>
			<? foreach($_sum_all as $account => $data): ?>
				<tr>
					<td><?= $account ?></td>
					<td>
						<? foreach($data as $currency => $amount): ?>
							<span class="<?= $amount > 0 ? 'inc' : 'exp' ?>"><?= round($amount, 2).'&nbsp;'.$currency ?></span>
						<? endforeach; ?>
					</td>
				</tr>
			<? endforeach; ?>
			</table><br>
			<strong>С выбранным фильтром</strong><br>
			<? foreach($_sum_filtered as $currency => $amount): ?>
				<span class="<?= $amount > 0 ? 'inc' : 'exp' ?>"><?= round($amount, 2).'&nbsp;'.$currency ?></span><br>
			<? endforeach; ?>
			
		</div>
		<!-- /итого -->
		
		<!-- планирование -->
		<div class="plans">
			<strong>Напоминания о предстоящих расходах и приходах</strong><br>
			<a href="/admin?module=kassa&page=2" class="reset">планирование &raquo;</a> <a href="/kassa/plans/" class="reset">прогноз &raquo;</a>
			<table class="optable" cellspacing="0">
			<? foreach($_plans as $date => $_dateplans): ?>
				<tr>
					<th colspan="3"><?= $date ?></th>
				</tr>
				<? foreach($_dateplans as $_plan): $bOdd = !$bOdd; ?>
					<tr class="<?= $_optypes_by_id[$_plan['operation_type_id']]['is_income'] ? 'inc' : 'exp' ?><?= $bOdd ? '_odd' : '' ?>">
						<td><?= $_plan['name'] ?></td>
						<td><?= $_plan['amount'] ?></td>
						<td><?= $_optypes_by_id[$_plan['operation_type_id']]['name'] ?></td>
					</tr>
				<? endforeach; ?>
			<? endforeach; ?>
			</table>
		</div>
		<!-- /планирование -->
	</div>
	<!-- /основная часть -->
	
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
			<select id="event_edit_form_backyear" name="backyear">
				<option value=""></option>
				<? for($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
					<option value="<?= $y ?>"><?= $y ?></option>
				<? endfor; ?>
			</select>
			<select id="event_edit_form_backmonth" name="backmonth">
				<option value=""></option>
				<? for($m = 1; $m <= 12; $m++): ?>
					<option value="<?= $m ?>"><?= $m ?></option>
				<? endfor; ?>
			</select>
			<select id="event_edit_form_backday" name="backday">
				<option value=""></option>
				<? for($d = 1; $d <= 31; $d++): ?>
					<option value="<?= $d ?>"><?= $d ?></option>
				<? endfor; ?>
			</select> <label>Запись задним числом</label>
			<br>
			<input type="hidden" name="id" id="event_edit_form_hidden">
			<input type="submit" value="Сохранить">
		</form>
	</div>
	<!-- /форма редактирования события -->
	
	<!-- форма переноса со счёта на счёт -->
	<div id="transaccount" class="container">
		<form action="<?= HTTP_ROOT ?>/kassa/tran_ac/" method="post" onsubmit="return onTransAccountSubmit();">
			<label for="transaccount_from">Со счёта:</label>
			<select name="account_from" id="transaccount_from">
				<? foreach($_accounts as $_a): ?>
					<option value="<?= $_a['id'] ?>"><?= $_a['name'] ?></option>
				<? endforeach; ?>
			</select>
			<label for="transaccount_from">На счёт:</label>
			<select name="account_to" id="transaccount_to">
				<? foreach($_accounts as $_a): ?>
					<option value="<?= $_a['id'] ?>"><?= $_a['name'] ?></option>
				<? endforeach; ?>
			</select>
			<label for="transaccount_sum">Сумма:</label>
			<input type="text" name="sum" id="transaccount_sum">
			<label for="transaccount_currency">Валюта:</label>
			<select name="currency" id="transaccount_currency">
				<? foreach($_currencies as $_c): ?>
					<option value="<?= $_c['id'] ?>"><?= $_c['symbol'].' '.$_c['name'] ?></option>
				<? endforeach; ?>
			</select>
			<label for="transaccount_comission">Комиссия, %:</label>
			<input type="text" name="comission" id="transaccount_comission">
			<br>
			<input type="submit" value="Перенести">
		</form>
	</div>
	<!-- /форма переноса со счёта на счёт -->
	
	<!-- ссылки -->
	<div id="linx" class="container">
		<strong>Ссылки</strong><br>
		<a href="/admin?module=kassa&page=1">настройки кассы</a><br>
		<a href="/kassa/stats/">статистика</a><br>
		<a href="/kassa/stats_graph/">графики</a><br>
		<a href="/kassa/plans/">планирование</a>
	</div>
	<!-- /ссылки -->
	
	<!-- калькулятор -->
	<div id="calculator">
		<input type="text" id="inp_calc" onkeypress="calc.inp_keypress(event)"><br>
		<table>
			<tr>
				<td onclick="calc.button(7)">7</td>
				<td onclick="calc.button(8)">8</td>
				<td onclick="calc.button(9)">9</td>
				<td onclick="calc.clear()">C</td>
			</tr>
			<tr>
				<td onclick="calc.button(4)">4</td>
				<td onclick="calc.button(5)">5</td>
				<td onclick="calc.button(6)">6</td>
				<td onclick="calc.button('/')">/</td>
			</tr>
			<tr>
				<td onclick="calc.button(1)">1</td>
				<td onclick="calc.button(2)">2</td>
				<td onclick="calc.button(3)">3</td>
				<td onclick="calc.button('*')">*</td>
			</tr>
			<tr>
				<td onclick="calc.button(0)">0</td>
				<td onclick="calc.button('.')">,</td>
				<td onclick="calc.button('+')">+</td>
				<td onclick="calc.button('-')">-</td>
			</tr>
		</table>
		<span class="button" onclick="calc.calculate();">= <small>(enter)</small></span>
		<span class="button" onclick="calc.calculate(); $('#calculator').fadeOut();">=<small>&amp;close (ctrl+enter)</small></span>
		<span class="button" onclick="$('#calculator').fadeOut();">exit<small> (esc)</small></span>
	</div>
	<!-- /калькулятор -->
	
	<!-- форма операции по долгам -->
	<div id="debtor_form">
		<span onclick="$('#debtor_form').fadeOut(300);">отмена</span><br>
		<img src="<?= HTTP_ROOT ?>/i/kassa/event_edit_form_arrow.gif">
		<form action="<?= HTTP_ROOT ?>/kassa/debtor/" method="post">
			<select id="debtor_operation" name="debtor_operation">
				<option value="0">я взял денег (-)</option>
				<option value="1">я дал денег (+)</option>
				<option value="2">изменить сумму долга (+/-)</option>
			</select> <label for="debtor_operation">Взял/дал в долг</label><br>
			<select name="debtor_currency" id="debtor_currency">
				<? foreach($_currencies as $_c): ?>
					<option value="<?= $_c['id'] ?>"><?= $_c['symbol'].' '.$_c['name'] ?></option>
				<? endforeach; ?>
			</select> <label for="debtor_currency">Валюта</label><br>
			<input type="text" id="debtor_amount" name="debtor_amount"> <label for="debtor_amount">Сколько денег</label><br>
			<input type="text" id="debtor_comment" name="debtor_comment"> <label for="debtor_comment">Комментарий</label><br>
			<input type="hidden" id="debtor_id" name="debtor_id">
			<input type="submit" value="Добавить">
		</form>
	</div>
	<!-- /форма операции по долгам -->
	
	<!-- форма добавления и редактирования холда -->
	<div id="hold_form">
		<img src="<?= HTTP_ROOT ?>/i/kassa/event_edit_form_arrow.gif">
		<span onclick="$('#hold_form').fadeOut(300);">отмена</span>
		<form action="<?= HTTP_ROOT ?>/kassa/hold/" method="post">
			<input type="hidden" name="id" id="hold_form_id">
			<label for="hold_form_optype">Тип операции</label><br>
			<select name="optype" id="hold_form_optype">
				<option value=""></option>
				<?
				foreach($_optypes as $_group)
				{
					foreach($_group as $_optype)
					{
						echo '<option value="'.$_optype['id'].'">'.$_optype['name'].'</option>';
					}
				}
				?>
			</select><br>
			<label for="hold_form_amount">Отложенная сумма</label><br>
			<input type="text" name="amount" id="hold_form_amount"><br>
			<label for="hold_form_currency">Валюта</label>
			<select name="currency" id="hold_form_currency">
				<? foreach($_currencies as $_c): ?>
					<option value="<?= $_c['id'] ?>"<?= $_c['default'] ? ' selected' : '' ?>><?= $_c['symbol'].' '.$_c['name'] ?></option>
				<? endforeach; ?>
			</select><br>
			<label for="hold_form_account">Счёт</label>
			<select name="account" id="hold_form_account">
				<? foreach($_accounts as $_a): ?>
					<option value="<?= $_a['id'] ?>"<?= $_a['default'] ? ' selected' : '' ?>><?= $_a['name'] ?></option>
				<? endforeach; ?>
			</select><br>
			<label for="hold_form_comment">Комментарий</label><br>
			<input type="text" name="comment" id="hold_form_comment" maxlength="255"><br>
			<input type="submit" value="Ок">
		</form>
	</div>
	<!-- /форма добавления и редактирования холда -->
	
</body>
</html>