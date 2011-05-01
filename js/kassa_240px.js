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