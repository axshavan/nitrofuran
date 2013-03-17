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
	// ...
}

/**
 * Убрать аяксовую занавесочку
 */
function curtainOff()
{
	// ...
}

/**
 * Показать аяксовую занавесочку
 */
function curtainOn()
{
	// ...
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