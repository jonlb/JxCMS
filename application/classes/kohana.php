<?php

class Kohana extends Kohana_Core {

    /**
     * This function will add JxCMS module paths to the $_paths array.
     *
     * @static
     * @param  $paths
     * @return void
     */
    public static function addPaths($paths) {
        $p = Kohana::$_paths;

        //Jx_Debug::dump($p, 'starting paths');

        //this should actually insert after JxCore and before anything else....
        $newPath = array();
        foreach ($p as $path) {
            $newPath[] = $path;
            if (strpos($path, 'jxCore')) {
                $newPath = array_merge($newPath, $paths);
            }
        }


        Kohana::$_paths = $newPath;

        //Jx_Debug::dump(Kohana::$_paths,'ending paths');
    }
}
