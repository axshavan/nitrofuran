/*
	Смена типа расписание в админке в форме редактирования события
	в планировании.
	@param repeattype {string}  тип расписания
	@param id         {integer} номер записи
*/
function adminPlanRepeatTypeChange(repeattype, id)
{
	ge('plan_' + id + '_repeat').value                = '';
	ge('repeattype_' + id + '_none').style.display    = 'none';
	ge('repeattype_' + id + '_daily').style.display   = 'none';
	ge('repeattype_' + id + '_weekly').style.display  = 'none';
	ge('repeattype_' + id + '_monthly').style.display = 'none';
	if(ge('repeattype_' + id + '_' + repeattype))
	{
		ge('repeattype_' + id + '_' + repeattype).style.display = 'block';
	}
}

/*
	Смена типа расписание в админке в форме добавления нового события
	в планировании.
	@param repeattype {string} тип расписания
*/
function adminPlanRepeatTypeChange2(repeattype)
{
	ge('kassa_addplan_form_repeat').value  = '';
	ge('repeattype_none').style.display    = 'none';
	ge('repeattype_daily').style.display   = 'none';
	ge('repeattype_weekly').style.display  = 'none';
	ge('repeattype_monthly').style.display = 'none';
	if(ge('repeattype_' + repeattype))
	{
		ge('repeattype_' + repeattype).style.display = 'block';
	}
}

/*
	Добавить или убрать день недели из инпута с расписанием в планировании.
	@param id   {int}  номер записи
	@param day  {int}  номер дня недели (1-Пн, 7-Вс)
	@param bAdd {bool} добавить или убрать
*/
function adminPlanRepeatTypeC1(id, day, bAdd)
{
	var inp = ge('plan_' + id + '_repeat');
	var schedule = new String(inp.value);
	if(schedule.indexOf(day) > -1 && !bAdd)
	{
		inp.value = schedule.replace(day, '');
	}
	else if(schedule.indexOf(day) < 0 && bAdd)
	{
		inp.value += day;
	}
}

/*
	Добавить или убрать день месяца из инпута с расписанием в планировании.
	@param id   {int}  номер записи
	@param day  {int}  номер дня месяца (1-31)
	@param bAdd {bool} добавить или убрать
*/
function adminPlanRepeatTypeC2(id, day, bAdd)
{
	var inp = ge('plan_' + id + '_repeat');
	var schedule = new String(inp.value);
	if(schedule.length)
	{
		schedule = schedule.split(',');
	}
	else
	{
		schedule = new Array();
	}
	if(schedule.indexOf(day) > -1 && !bAdd)
	{
		schedule[schedule.indexOf(day)] = '';
		inp.value = schedule.toString();
	}
	else if(schedule.indexOf(day) < 0 && bAdd)
	{
		inp.value += ',' + day;
	}
}

/*
	Добавить или убрать день недели из инпута с расписанием в планировании.
	@param day  {int}  номер дня недели (1-Пн, 7-Вс)
	@param bAdd {bool} добавить или убрать
*/
function adminPlanRepeatTypeD1(day, bAdd)
{
	var inp = ge('kassa_addplan_form_repeat');
	var schedule = new String(inp.value);
	if(schedule.indexOf(day) > -1 && !bAdd)
	{
		inp.value = schedule.replace(day, '');
	}
	else if(schedule.indexOf(day) < 0 && bAdd)
	{
		inp.value += day;
	}
}

/*
	Добавить или убрать день месяца из инпута с расписанием в планировании.
	@param day  {int}  номер дня месяца (1-31)
	@param bAdd {bool} добавить или убрать
*/
function adminPlanRepeatTypeD2(day, bAdd)
{
	var inp = ge('kassa_addplan_form_repeat');
	var schedule = new String(inp.value);
	if(schedule.length)
	{
		schedule = schedule.split(',');
	}
	else
	{
		schedule = new Array();
	}
	if(schedule.indexOf(day) > -1 && !bAdd)
	{
		schedule[schedule.indexOf(day)] = '';
		inp.value = schedule.toString();
	}
	else if(schedule.indexOf(day) < 0 && bAdd)
	{
		inp.value += day + ',';
	}
}

/*
	Проверка данных перед окончательной отправкой формы.
*/
function checkAddForm()
{
	var obj = ge('inp_optype');
	if(obj)
	{
		if(!obj.value)
		{
			alert('Укажите тип добавляемой записи');
			return false;
		}
	}
	obj = ge('inp_account');
	if(obj)
	{
		if(!obj.value)
		{
			alert('Укажите счёт');
			return false;
		}
	}
	obj = ge('inp_currency');
	if(obj)
	{
		if(!obj.value)
		{
			alert('Укажите валюту');
			return false;
		}
	}
	obj = ge('inp_amount');
	if(obj)
	{
		if(!obj.value)
		{
			alert('Введите сумму');
			return false;
		}
	}
	return true;
}

/*
	Сокращённая форма document.getElementById().
	@param id {string} идентификатор объекта
*/
function ge(id)
{
	return document.getElementById(id);
}

/*
	Обработка события onkeyup в инпуте с комментарием.
*/
function onCommentKeyUp()
{
	var obj = ge('inp_comment');
	if(!obj)
	{
		return;
	}
	var val = obj.value;
	if(!val || val.length < 3)
	{
		ge('div_comment_tip_content').innerHTML = '';
		$('#div_comment_tip').slideUp(300);
		return;
	}
	jQuery.post
	(
		'/kassa/ajax/',
		{
			mode:    'comment',
			comment: val
		},
		function(data)
		{
			ge('div_comment_tip_content').innerHTML = data;
			$('#div_comment_tip').slideDown(300);
		}
	);
}

/*
	Обработка нажатия на часто используемый тип операций.
	@param type_id {int} идентификатор типа
	@param group_id {int} идентификатор группы типа
*/
function onFrequentTypeClick(type_id, group_id)
{
	if(type_id && group_id)
	{
		onTypeGroupClick(ge('span_group_' + group_id), group_id);
		onTypeClick(ge('span_type_' + type_id), type_id);
	}
}

/*
	Обработка подтверждения формы переноса денег со счёта на счёт.
*/
function onTransAccountSubmit()
{
	if(ge('transaccount_from').value == ge('transaccount_to').value)
	{
		alert('Счета должны быть разными');
		return false;
	}
	if(!ge('transaccount_sum').value)
	{
		alert('Укажите сумму');
		return false;
	}
	return true;
}

/*
	Обработка нажатия на "тип операции".
	@param obj {HTML Element} объект, вызвавший событие
	@param type_id {integer} идентификатор типа
*/
function onTypeClick(obj, type_id)
{
	ge('inp_optype').value = type_id;
	$('.selected').removeClass('selected');
	obj.className += ' selected';
	ge('inp_amount').focus();
}

/*
	Обработка нажатия на "группу типов операций".
	@param obj {HTML Element} объект, вызвавший событие
	@param group_id {integer} идентификатор группы типов
*/
function onTypeGroupClick(obj, group_id)
{
	if(obj.className != 'selected_g')
	{
		$('.selected_g').removeClass('selected_g');
		obj.className = 'selected_g';
		$('.optypegroup').slideUp(300);
		$('#optypegroup' + group_id).slideDown(300);
	}
	$('.selected').removeClass('selected');
}

/*
	Обработка нажатия на "группу типов операций" в фильтре по типам.
	@param obj {HTML Element} объект, вызвавший событие
	@param group_id {integer} идентификатор группы типов
*/
function onTypeGroupClick2(obj, group_id)
{
	if(obj.className != 'selectedf_g')
	{
		$('.selectedf_g').removeClass('selectedf_g');
		obj.className = 'selectedf_g';
		$('.optypegroupf').slideUp(300);
		$('#optypegroupf' + group_id).slideDown(300);
	}
	$('.selectedf').removeClass('selectedf');
}

/*
	Установить значение комментария в форме добавления.
	@param {string} val
*/
function setComment(val)
{
	var obj = ge('inp_comment');
	if(obj)
	{
		obj.value = val;
	}
	$('#div_comment_tip').slideUp(300);
}

/*
	Показать форму добавления/списания/увеличения долга.
	@param obj {HTML Element} элемент, возле которого показывать форму
	@param debtor_id {integer} номер должника, к которому привяжется операция
*/
function showDebtorForm(obj, debtor_id)
{
	$('#debtor_form').css('top',  $(obj).offset().top + 36);
	$('#debtor_form').css('left', $(obj).offset().left - 11);
	ge('debtor_id').value = debtor_id;
	$('#debtor_form').fadeIn(300);
}

/*
	Обработка нажатия на кнопку "редактировать операцию".
	@param obj {HTML Element} объект, вызвавший событие
	@param event_params {array} параметры операции
*/
function startEditEvent(obj, event_params)
{
	var backtime = new Date(event_params['backtime'] * 1000);
	ge('event_edit_form_hidden').value    = event_params['id'];
	ge('event_edit_form_optype').value    = event_params['optype'];
	ge('event_edit_form_currency').value  = event_params['currency'];
	ge('event_edit_form_amount').value    = event_params['amount'];
	ge('event_edit_form_comment').value   = event_params['comment'];
	ge('event_edit_form_account').value   = event_params['account'];
	ge('event_edit_form_backyear').value  = backtime.getFullYear();
	ge('event_edit_form_backmonth').value = backtime.getMonth() + 1;
	ge('event_edit_form_backday').value   = backtime.getDate();
	$('#event_edit_form').css('top', $(obj).offset().top + 36);
	$('#event_edit_form').css('left', $(obj).offset().left - 11);
	$('#event_edit_form').fadeIn(300);
}

/*
	Калькулятор
*/
calc = {
	// объект, к которому привязан калькулятор
	bindobj: null,
	
	/*
		Добавить к значению в поле калькулятора строку.
		@param value {string} строка, которую добавить
	*/
	button: function(value)
	{
		ge('inp_calc').value += value;
		ge('inp_calc').focus();
	},
	
	/*
		Вычисление значения.
	*/
	calculate: function()
	{
		var process_str = ge('inp_calc').value + '=';
		var current_sum = 0;
		var operand     = '';
		var current_char;
		var prev_op     = '+';
		for(var c in process_str)
		{
			current_char = process_str[c];
			if(parseInt(current_char) || current_char === '0')
			{
				operand += current_char;
			}
			else
			{
				if(
					current_char == '+' ||
					current_char == '-' ||
					current_char == '*' ||
					current_char == '/' ||
					current_char == '='
				)
				{
					operand = parseFloat(operand);
					if(operand)
					{
						switch(prev_op)
						{
							case '+': current_sum += operand; break;
							case '-': current_sum -= operand; break;
							case '*': current_sum *= operand; break;
							case '/': current_sum /= operand; break;
						}
					}
					prev_op     = current_char;
					current_sum = parseFloat(current_sum);
					operand     = '';
				}
				else if(
					current_char == '.' ||
					current_char == ','
				)
				{
					operand += '.';
				}
			}
		}
		this.bindobj.value   = Math.round(current_sum * 100) / 100;
		ge('inp_calc').value = current_sum;
		ge('inp_calc').focus();
	},
	
	/*
		Очистить содержимое калькулятора.
	*/
	clear: function()
	{
		ge('inp_calc').value = '';
		ge('inp_calc').focus();
	},
	
	inp_keypress: function(event)
	{
		if(event.keyCode == 13)
		{
			this.calculate();
			if(event.ctrlKey)
			{
				$('#calculator').fadeOut(300);
			}
		}
		else if(event.keyCode == 27)
		{
			$('#calculator').fadeOut(300);
		}
	},
	
	/*
		Показать калькулятор.
		@param obj {HTML Input Element} объект, к которому привяжется калькулятор
	*/
	show: function(obj)
	{
		if(obj)
		{
			this.bindobj = obj;
			$('#calculator').css('top', $(obj).offset().top + 20);
			$('#calculator').css('left', $(obj).offset().left);
			$('#calculator').fadeIn(300);
			ge('inp_calc').value = '';
			ge('inp_calc').focus();
		}
	}
};

Array.prototype.indexOf = function(val)
{
	for(var i = this.length - 1; i >=0 ; i--)
	{
		if(this[i] == val)
		{
			return i;
		}
	}
	return -1;
}