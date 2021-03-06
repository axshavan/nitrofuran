/* 01.11.2014 */

var maxTypeGroupId = 0;

/*
	Замена document.getElementById.
	@param  String id  айдишник элемента
	@return HTMLObject элемент
*/
function a(id)
{
	return document.getElementById(id);
}

/*
	Проверка полей перед отправкой формы.
*/
function onFormSubmit()
{
	var obj = a('inp_amount');
	if(obj && !obj.value.length)
	{
		alert('Сумма?');
		return false;
	}
	obj = a('inp_optype');
	if(obj && !obj.value)
	{
		alert('Тип операции?');
		return false;
	}
	return true;
}

/*
	Изменение селекта группы типов операций.
	@param String val значение селекта
*/
function onGroupSelectChange(val)
{
	var obj;
	for(var i = 0; i <= maxTypeGroupId; i++)
	{
		obj = a('inp_optype' + i);
		if(obj)
		{
			if(val == i)
			{
				obj.style.display = 'inline-block';
			}
			else
			{
				obj.style.display = 'none';
			}
		}
	}
}

/*
	Измнение селекта типа операции.
	@param String val значение селекта
*/
function onTypeSelectChange(val)
{
	var obj = a('inp_optype');
	if(obj)
	{
		obj.value = val;
	}
}

/*
	Установить значение комментария в форме добавления.
	@param {string} val
*/
function setComment(val)
{
	var obj = a('inp_comment');
	if(obj)
	{
		obj.value = val;
	}
	obj = a('div_comment_tip');
	if(obj)
	{
		obj.style.display = 'none';
	}
}