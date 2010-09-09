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