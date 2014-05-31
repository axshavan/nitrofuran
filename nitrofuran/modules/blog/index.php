<?php

/**
 * Основная страница модуля blog
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/blog.php');
require_once(DOCUMENT_ROOT.'/nitrofuran/modules/blog/blog_post.php');

// блог
$_blog = CBlog::GetList(array('tree_id' => $TREE_INFO['current']['id']));
if(!isset($_blog[0]) || !$_blog[0]['id'])
{
	error404();
}
$_blog = $_blog[0];
trace($_blog);

// посты в блог
$_params = array();
// ...
$_posts = CBlogPost::GetList(array('blog_id' => $_blog['id']), array('date_create' => 'desc'), $_params);
trace($_posts);

?>
