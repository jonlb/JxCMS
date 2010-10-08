<?php


Route::set('page', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'page',
		'action'     => 'view',
	));
 
