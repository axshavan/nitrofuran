/* 01.11.2014 */

var bTabPressed = false;

/*
	Переход фокуса на сабмит формы.
*/
function onSubmitFocus()
{
	if(bTabPressed)
	{
		var textarea = ge('content');
		if(textarea)
		{
			bTabPressed = false;
			textarea.focus();
		}
	}
}

/*
	Нажатие кнопки внутри текстарии.
	@param {HTMLTextAreaObject} textarea текстареа
	@param {int}                keyCode  код нажатой кнопки
*/
function onTextareaKeyPress(textarea, keyCode)
{
	switch(keyCode)
	{
		case 9:
		{
			// TAB
			textarea.focus();
			bTabPressed = true;
			break;
		}
	}
}