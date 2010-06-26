<?php

if (!defined('DS')) {
    define('DS',DIRECTORY_SEPARATOR);
}

//change session to use database
Session::$default = 'database';

//we need to go through and initialize all of the modules we need to use

Jx_Modules::init();


//Jx_Debug::dump(Route::all(),'Defined routes');
