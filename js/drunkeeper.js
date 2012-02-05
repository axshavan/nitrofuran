/**
 * Обработка изменения типа напитка в форме добавления.
 * @param {Int} drink_type_id ид категории напитков
 */
function onDrinkTypeChange(drink_type_id)
{
	var obj, objs;
	objs = document.getElementsByTagName('div');
	for(var i in objs)
	{
		if(objs[i] && objs[i].className == 'drink2')
		{
			objs[i].style.display = 'none';
		}
	}
	obj = document.getElementById('drunkeeper_form_drink' + drink_type_id + '_div');
	if(obj)
	{
		obj.style.display = 'block';
	}
}