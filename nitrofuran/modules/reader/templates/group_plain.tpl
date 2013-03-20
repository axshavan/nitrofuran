<?php

function draw_subscriptions_recursively2(&$tree, $parent_name)
{
	if(!sizeof($tree) || !is_array($tree))
	{
		return;
	}
	if($tree['data']['name'])
	{
		$parent_name .= '/'.$tree['data']['name'];
	}
	else
	{
		$parent_name = 'root';
	}
	echo '<option value="'.(int)$tree['data']['id'].'">'.h($parent_name).'</option>';
	foreach($tree['children'] as &$child)
	{
		draw_subscriptions_recursively2($child, $parent_name);
	}
}
draw_subscriptions_recursively2($tree, '');

?>