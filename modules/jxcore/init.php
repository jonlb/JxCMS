<?php

if (!defined('DS')) {
    define('DS',DIRECTORY_SEPARATOR);
}

//we need to go through and initialize all of the modules we need to use

Jx_Modules::init();

Route::add('media','media/<action>/<file>(.<ext>)',null,'admin')
    ->defaults(array(
        'controller' => 'media'
    ));


//add event callbacks as needed
Jx_Event::addObserver(array('Jx_Modules','onGetAdminMenu'),'getAdminMenu');
Jx_Event::addObserver(array('Jx_Settings','onGetAdminMenu'),'getAdminMenu');