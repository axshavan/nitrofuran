<?php

/**
 * Админская страница модуля blog
 * @author Dmitry Nikiforov <axshavan@yandex.ru>
 * @license http://sam.zoy.org/wtfpl WTFPL
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://sam.zoy.org/wtfpl/COPYING for more details.
 */

$te = new CTemplateEngine('blog');
$te->assign('_left_menu', array
	(
		'index'    => array('active' => false, 'name' => 'Назад в админку', 'href' => '/admin/'),
		'bloglist' => array('active' => false, 'name' => 'Блоги',           'href' => '?page=bloglist')
	)
);
switch($_GET['page'])
{
	case 'blogadd':
	{
		// ...
		break;
	}
	case 'bloglist':
	default:
	{
		$te->_tpl_vars['_left_menu']['bloglist']['active'] = true;
		$te->assign('inner_template_name', 'a/bloglist.tpl');
		// ...
		break;
	}
}
$te->template('a/admin.tpl');

?>