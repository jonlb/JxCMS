<?php


Route::add('users', 'user(/<action>)', null, 'admin')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'login',
	));

Route::add('user_admin','admin/user/<action>(.<format>)', array('format'=>'html|xml|json|rss'), 'admin')
    ->defaults(array(
        'directory' => 'admin',
        'controller' => 'user',
        'format' => 'json'
    ));
