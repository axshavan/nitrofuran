<?php

function draw_subscriptions_recursively(&$tree)
{
	if(!sizeof($tree) || !is_array($tree))
	{
		return;
	}
	echo '<ul class="folders">';
	foreach($tree['children'] as &$item)
	{
		echo '<li><span onclick="showSubscriptionGroup(this, '.$item['data']['id'].')">'.$item['data']['name'].'</span><div>';
		draw_subscriptions_recursively($item);
		echo '</div></li>';
	}
	echo '</ul><ul class="feeds">';
	foreach($tree['data']['subscriptions'] as &$subscription)
	{
		echo '<li '.($subscription['unread_count'] ? 'class="unread"' : '').' onclick="showSubscribtion(this, '.$subscription['id'].')">'.$subscription['name']
			.'<span class="unread">'.($subscription['unread_count'] ? ' ('.$subscription['unread_count'].')' : '').'</span>'
			.'<span class="delete" onclick="deleteSubscription(this, '.$subscription['id'].')"></span>'
			.'</li>';
	}
	echo '</ul>';
}
draw_subscriptions_recursively($tree);

?>
