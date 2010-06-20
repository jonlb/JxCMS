<?php defined('SYSPATH') or die('No direct script access.');

class Route extends Kohana_Route {

    /**
     * Adds a route to the array of routes with the option of placing it before a specific
     * other named route in the array.
     * @static
     * @param  $name
     * @param  $uri
     * @param  $regex
     * @param  $before
     * @return void
     */
    public static function add($name, $uri, array $regex = NULL, $before = null) {

        if (empty($before)) {
            return parent::set($name, $uri, $regex);
        }

        $keys = array_keys(Route::$_routes);
        //Jx_Debug::dump($keys,'route keys before replacement');
        $index = array_search($before,$keys);

        //Jx_Debug::dump($index,'key index');

        if (false !== $index) {
            //Jx_Debug::dump('in right spot');
            $values = array_values(Route::$_routes);
            //Jx_Debug::dump($values,'values before replacement');
            array_splice($keys, $index, 0, $name);
            //Jx_Debug::dump($keys,'route keys after replacement');
            array_splice($values, $index, 0, array(new Route($uri, $regex)));
            //Jx_Debug::dump($values,'values after replacement');
            Route::$_routes = array_combine($keys, $values);
            //Jx_Debug::dump(Route::$_routes,'routes after adding test');
            return Route::$_routes[$name];
        } else {
            //Jx_Debug::dump('in bad spot');
            return parent::set($name, $uri, $regex); 
        }


    }
}