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
	Обработка события onkeyup в инпуте с комментарием.
*/
function onCommentKeyUp(event)
{
	var obj = a('inp_comment');
	if(!obj)
	{
		return;
	}
	var val = obj.value;
	if(!val || val.length < 3)
	{
		a('div_comment_tip_content').innerHTML = '';
		a('div_comment_tip').style.display = 'none';
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
			a('div_comment_tip_content').innerHTML = data;
			if(data.length)
			{
				$('#div_comment_tip').slideDown(300);
			}
			else
			{
				$('#div_comment_tip').slideUp(300);
			}
		}
	);
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