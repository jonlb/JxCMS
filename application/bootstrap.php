<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------



/**
 * Set the default time zone.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('America/Los_Angeles');

/**
 * Set the default locale.
 *
 * @see  http://kohanaframework.org/guide/using.configuration
 * @see  http://php.net/setlocale
 */
setlocale(LC_ALL, 'en_US.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://kohanaframework.org/guide/using.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url'   => '/',
	'index_file' => FALSE,
));

//define production
define('IN_PRODUCTION',false);
/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(APPPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

//var_dump('about to init modules');
/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array(

	'cache'      => MODPATH.'cache',      // Caching with multiple backends
	// 'codebench'  => MODPATH.'codebench',  // Benchmarking tool
    'firephp'   => MODPATH.'firephp',
	'database'   => MODPATH.'database',   // Database access
	'image'      => MODPATH.'image',      // Image manipulation
    'twig'          => MODPATH.'twig',          //template language
	'jelly'        => MODPATH.'jelly',        // Object Relationship Mapping
    'jxcore'        => MODPATH.'jxcore',        // the core of JxCMS
    'jelly-auth'    => MODPATH.'jelly-auth',    
    'auth'       => MODPATH.'auth',       // Basic authentication

	// 'pagination' => MODPATH.'pagination', // Paging of results
	'userguide'  => MODPATH.'userguide'  // User guide and API documentation
	));

/**
 * Attach FirePHP to logging. be sure to enable firephp module
 */

// Exclude all FirePHP console logs from the file log...

Kohana::$log->attach(new FirePHP_Log_File(APPPATH.'logs'));
Kohana::$log->attach(new FirePHP_Log_Console());
//Kohana::$log->add('FirePHP::INFO', 'FirePHP Initialized...')->write();


/**
 * Set the routes. Each route must have a minimum of a name, a URI and a set of
 * defaults for the URI.
 */
/*
Route::set('default', '(<controller>(/<action>(/<id>)))')
	->defaults(array(
		'controller' => 'welcome',
		'action'     => 'index',
	));
*/
//Jx_Debug::dump(Route::all(),'Defined routes');

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
echo Request::instance() 	
    ->execute()
	->send_headers()
	->response;


FirePHP_Profiler::instance()
	->group('KO3 FirePHP Profiler Results:')
	->superglobals() // New Superglobals method to show them all...
	->database()
	->benchmark()
	->groupEnd();
