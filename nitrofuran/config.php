<?php

/*
 * Конфигурационный файл.
 */

define('HTTP_ROOT',     ''); // размещение корня сайта с точки зрения сервера
define('DOCUMENT_ROOT', $_SERVER['DOCUMENT_ROOT'].HTTP_ROOT); // физическое размещение корня сайта

// параметры соединения с базой данных
define('MYSQL_HOST',     'localhost');
define('MYSQL_USER',     'nitrofuran');
define('MYSQL_PASSWORD', '123');
define('MYSQL_DATABASE', 'nitrofuran');

// имена некоторых таблиц
define('TREE_TABLE',     'tree');
define('USERS_TABLE',    'users');
define('SESSIONS_TABLE', 'sessions');
define('PARAMS_TABLE',   'params');

// пользовательские сессии
define('SESSION_COOKIE_NAME', 'iliketomoveitmoveit');
define('SESSION_LIFETIME',    86400 * 14); // 2 weeks

?>