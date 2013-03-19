var bAjaxInProgress = false;

$(document).ready
(
	function()
	{
		// загрузка списка подписок
		loadSubscriptions();
	}
);

/**
 * Засабмиттить форму добавления группы подписок
 */
function addGFormSubmit()
{
	var groupname = $('#addgform input').val();
	if(!groupname || !groupname.length)
	{
		return false;
	}
	if(bAjaxInProgress)
	{
		return;
	}
	bAjaxInProgress = true;
	curtainOn();
	jQuery.post
	(
		curpath,
		{
			ajax: 'addGroup',
			group: groupname,
			parent_id: 0
		},
		function(data)
		{
			if(data == 'ok')
			{
				$('#addgform').fadeOut();
				$('#addgform input').val('')
				loadSubscriptions();
			}
			else
			{
				curtainOff();
				alert(data);
			}
		}
	);
}

/**
 * Засабмиттить форму добавления подписки
 */
function addSFormSubmit()
{
	var subscrhref = $('#addsform input').val();
	if(!subscrhref || !subscrhref.length)
	{
		return false;
	}
	if(bAjaxInProgress)
	{
		return;
	}
	bAjaxInProgress = true;
	curtainOn();
	jQuery.post
	(
		curpath,
		{
			ajax: 'addSubscription',
			href: subscrhref,
			group_id: 0
		},
		function(data)
		{
			if(data == 'ok')
			{
				$('#addsform').fadeOut();
				$('#addsform input').val('')
				loadSubscriptions();
			}
			else
			{
				curtainOff();
				alert(data);
			}
		}
	);
}

/**
 * Убрать аяксовую занавесочку
 */
function curtainOff()
{
	bAjaxInProgress = false;
	$('#curtain').fadeOut();
}

/**
 * Показать аяксовую занавесочку
 */
function curtainOn()
{
	$('#curtain').fadeIn();
}

/**
 * Загрузить список подписок в соответствующий див
 */
function loadSubscriptions()
{
	curtainOn();
	jQuery.post
	(
		curpath,
		{
			ajax: 'loadSubscriptions'
		},
		function(data)
		{
			curtainOff();
			$('#subscr').html(data);
		}
	);
}