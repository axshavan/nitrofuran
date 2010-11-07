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
	Обработка нажатия на кнопку "редактировать операцию".
	@param obj {HTML Element} объект, вызвавший событие
	@param event_params {array} параметры операции
*/
function startEditEvent(obj, event_params)
{
	ge('event_edit_form_hidden').value   = event_params['id'];
	ge('event_edit_form_optype').value   = event_params['optype'];
	ge('event_edit_form_currency').value = event_params['currency'];
	ge('event_edit_form_amount').value   = event_params['amount'];
	ge('event_edit_form_comment').value  = event_params['comment'];
	ge('event_edit_form_account').value  = event_params['account'];
	$('#event_edit_form').css('top', $(obj).offset().top + 36);
	$('#event_edit_form').css('left', $(obj).offset().left - 11);
	$('#event_edit_form').fadeIn(300);
}

/*
 * Калькулятор
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
			if(parseInt(current_char))
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
				$('#calculator').fadeOut();
			}
		}
		else if(event.keyCode == 27)
		{
			$('#calculator').fadeOut();
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
			$('#calculator').fadeIn();
			ge('inp_calc').value = '';
			ge('inp_calc').focus();
		}
	}
};