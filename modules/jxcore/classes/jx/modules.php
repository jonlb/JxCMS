<?php

class Jx_Modules {

    protected static $_modules;

    public static function init() {
        //get list of all registered modules
        self::$_modules = Jelly::select('module')->execute()->as_array('name');
        //Jx_Debug::dump(self::$_modules,'module listing');

        $paths = array();
        //go through the modules directory and get directory names
        $iter = new DirectoryIterator(MODPATH);
        foreach ($iter as $f) {
            if (!$f->isDot() && $f->isDir()) {
                //compare with registered modules
                $fname = $f->getFilename();
                if (self::isRegistered($fname)) {
                    if (self::isActivated($fname) && !self::isPermanent($fname)){
                        //if registered, activated, and not permanent, run init.php for the module
                        //permanent modules are initialized by Kohana directly.
                        $path = MODPATH.$fname;
                        $paths[] = realpath(MODPATH.$fname).DS;
                    }
                } else {
                    self::register($fname);
                }

            }
        }

        Kohana::addPaths($paths);
        foreach ($paths as $path) {
            if (is_file($path.'init.php')) {
                require_once $path.'init.php';
            }
        }

    }

    /**
     * Checks to see if a module has been activated yet
     * @static
     * @param  $name
     * @return void
     */
    public static function isActivated($name) {
        return (bool) self::$_modules[$name]['activated'];
    }

    /**
     * Checks to see if a module has been registered (added to the database)
     * @static
     * @param  $name
     * @return bool
     */
    public static function isRegistered($name) {
        return array_key_exists($name,self::$_modules);
    }

    public static function isPermanent($name) {
        return (bool) self::$_modules[$name]['permanent'];
    }

    /**
     * Registers a module in the database but does not activate it
     * @static
     * @param  $name
     * @return void
     */
    public static function register($name) {
        Jelly::factory('module')
            ->set(array(
                'name' => $name
            ))->save();
    }

    public static function get_all(){
        return self::$_modules;
    }

    public static function getVersion($name) {
        $class = 'Jx_'.ucfirst($name);
        if (class_exists($class) && is_callable(array($class, 'getVersion'))) {
            return call_user_func(array($class, 'getVersion'));
        } else {
            return call_user_func(array('Jx_Core','getVersion'));
        }
    }

    public static function onGetAdminMenu(Jx_Event_Notification $notification) {
        if ($notification->hasReturnData()) {
            $menu = $notification->getReturnData();
        } else {
            $menu = $notification->getObject();
        }


        $menu['system']['submenu']['modules'] = array(
            'text' => 'Manage Modules',
            'title' => '',
            'file' => 'modules'
        );

        $notification->setReturnData($menu);
    }
}
