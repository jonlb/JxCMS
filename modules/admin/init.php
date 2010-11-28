<?php

//we would need to construct ones for each module...


//set the admin route
Route::set('admin', 'admin(/<controller>(/<action>(.<format>)))')
	->defaults(array(
        'directory' => 'admin',
		'controller' => 'index',
		'action'     => 'index',
        'format'    => 'html',
	));



