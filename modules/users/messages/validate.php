<?php

return array(
    'username' => array(
	    'not_empty'  => 'usename can not be empty.',
		'min_length' => 'username must be at least 4 characters',
		'max_length' => 'username can not be longer than 42 characters',
		'regex'      => 'username can only contain...',
	),
	'password' => array(
		'not_empty'  => 'password can not be empty.',
		'min_length' => 'password must be at least 5 characters',
		'max_length' => 'password can not be longer than 42 characters',
	),
	'password_confirm' => array(
		'matches'    => 'password and confirm password must match.',
	),
	'email' => array(
		'not_empty'  => '',
		'min_length' => '4',
		'max_length' => '127',
		'email'      => '',
	)
);
