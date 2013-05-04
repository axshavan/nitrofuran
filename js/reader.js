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
		return false;
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
		return false;
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
				$('#addsform input').val('');
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
 * Убрать аяксовую занавесочку-2
 */
function curtain2Off()
{
	$('#rightcurtain').fadeOut();
}

/**
 * Показать аяксовую занавесочку
 */
function curtainOn()
{
	$('#curtain').fadeIn();
}

/**
 * Показать аяксовую занавесочку-2
 */
function curtain2On()
{
	$('#rightcurtain').fadeIn();
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
			data = jQuery.parseJSON(data);
			$('#subscr').html(data['subscr']);
			$('#editsform_group').html(data['editsform_group']);
			$('#editgform_group').html(data['editsform_group']);
		}
	);
}

/**
 * Отрабатывает при скроллинге правого дива
 */
function onRightDivScroll()
{
	$('#right').children('div.item').each
	(
		function(k, v)
		{
			if($(v).offset().top <= 50)
			{
				// как только верхушка div.item уезжает за верхнюю границу, помечаем элемент активным и прочитанным
				if(!$(v).hasClass('active'))
				{
					$(v).addClass('active');
					if(bAjaxInProgress)
					{
						return false;
					}
					bAjaxInProgress = true;
					jQuery.post
					(
						curpath,
						{
							ajax: 'markAsRead',
							item_id: v.id.substr(5)
						},
						function(data)
						{
							bAjaxInProgress = false;
							if(data != 'ok')
							{
								alert(data);
							}
						}
					);
				}
			}
			else
			{
				$(v).removeClass('active');
			}
		}
	);
}

/**
 * Сохранить подписку из формы редактирования подписки
 */
function saveSubscription()
{
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
			ajax:     'saveSubscription',
			id:       $('#editsform_id').val(),
			group_id: $('#editsform_group').val(),
			name:     $('#editsform_name').val()
		},
		function(data)
		{
			if(data == 'ok')
			{
				$('#editsform').fadeOut();
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
 * Сохранить группу подписок
 */
function saveSubscriptionGroup()
{
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
			ajax:     'saveSubscriptionGroup',
			id:       $('#editgform_id').val(),
			group_id: $('#editgform_group').val(),
			name:     $('#editgform_name').val()
		},
		function(data)
		{
			if(data == 'ok')
			{
				$('#editgform').fadeOut();
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
 * Пометить элемент прочитанным и скрыть его
 * @param id идентификатор элемента
 */
function setItemRead(id)
{
	curtain2On();
	jQuery.post
		(
			curpath,
			{
				ajax:    'markAsRead',
				item_id: id
			},
			function(data)
			{
				curtain2Off();
				if(data == 'ok')
				{
					$('#item_' + id).fadeOut();
				}
				else
				{
					alert(data);
				}
			}
		);
}

/**
 * Показать подписку
 * @param obj ListItem
 * @param id  идентификатор подписки
 */
function showSubscribtion(obj, id)
{
	if(bAjaxInProgress)
	{
		return;
	}
	curtainOn();
	bAjaxInProgress = true;
	$('.left *').removeClass('active');
	$(obj).addClass('active');
	$('#right')[0].scrollTop = 0;
	jQuery.post
	(
		curpath,
		{
			ajax: 'getSubsription',
			id: id
		},
		function(data)
		{
			data = jQuery.parseJSON(data);
			$('.header .editform').hide();
			$('#editsform').fadeIn();
			$('#editsform_href').text(data['href']);
			$('#editsform_id').val(data['id']);
			$('#editsform_group').val(data['group_id']);
			$('#editsform_name').val(data['name']);
			curtainOff();
			curtain2On();
			jQuery.post
			(
				curpath,
				{
					ajax: 'getSubscriptionItems',
					id: id
				},
				function(data)
				{
					curtain2Off();
					$('#right').html(data);
				}
			);
		}
	);
}

/**
 * Показать группу подписок
 * @param obj ListItem
 * @param id  идентификатор группы подписок
 */
function showSubscriptionGroup(obj, id)
{
	if(bAjaxInProgress)
	{
		return;
	}
	bAjaxInProgress = true;
	curtainOn();
	$('.left *').removeClass('active');
	$(obj).addClass('active');
	jQuery.post
	(
		curpath,
		{
			ajax: 'getSubsriptionGroup',
			id: id
		},
		function(data)
		{
			$('.header .editform').hide();
			$('#editgform').fadeIn();
			data = jQuery.parseJSON(data);
			$('#editgform_id').val(data['id']);
			$('#editgform_group').val(data['group_id']);
			$('#editgform_name').val(data['name']);
			curtainOff();
		}
	);
}