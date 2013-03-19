<?php

function draw_subscriptions_recursively(&$tree)
{
	if(!sizeof($tree) || !is_array($tree))
	{
		return;
	}
	echo '<ul class="folders">';
	foreach($tree as &$item)
	{
		echo '<li>'.$item['data']['name'].'<div>';
		draw_subscriptions_recursively($item['children']);
		if(sizeof($item['data']['subscriptions']) && is_array($item['data']['subscriptions']))
		{
			echo '<ul class="feeds">';
			foreach($item['data']['subscriptions'] as &$subscription)
			{
				echo '<li>'.$subscription['name'].'</li>';
			}
			echo '</ul>';
		}
		echo '</div></li>';
	}
	echo '</ul>';
}
draw_subscriptions_recursively($tree['children']);

?>
