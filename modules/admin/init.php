<?php

//we would need to construct ones for each module...


//set the admin route
Route::set('admin', 'admin(/<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'index',
		'action'     => 'index',
	));



