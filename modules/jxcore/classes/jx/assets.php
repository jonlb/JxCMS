<?php

class Jx_Assets {

    private static $_stacks = array(
        'css' => array(),
        'js' => array()
    );

    public static function add_js_file($filename,$stack,$name,$after = null) {
        //build filename
        if ($filename[0] !== '/') {
            $filename = 'media/js/'.$filename;
        } else {
            $filename = substr($filename, 1);
        }
        $script = htmlspecialchars_decode(HTML::script($filename), ENT_QUOTES);
        //Jx_Debug::dump($script, 'script we are adding');

        self::add_to_stack($script,'js',$stack,$name,$after);
    }

    public static function add_js_script($script, $stack, $name, $after = null) {
        $script = '<script type="text/javascript">'.$script.'</script>';
        self::add_to_stack($script,'js', $stack, $name, $after);
    }

    public static function add_css_file($filename, $stack, $name, $after = null) {
        //build filename
        $filename = 'media/css/'.$filename.'.css';
        $script = HTML::style($filename);
        self::add_to_stack($script,'css',$stack,$name,$after);
    }

    public static function add_css_styles($styles, $stack, $name, $after = null) {
        $style = '<style>'.$styles.'</style>';
        self::add_to_stack($style,'css', $stack, $name, $after);
    }

    public static function render_js($name) {
        return Jx_Assets::render('js', $name);
    }

    public static function render_css($name) {
        return Jx_Assets::render('css', $name);
    }

    
    public static function render($type, $name) {
        $type_array = self::$_stacks[$type];
        if (isset($type_array[$name])) {
            return implode("\n",$type_array[$name]);
        }
        return '';
    }


    private static function add_to_stack($obj, $type, $stack, $name, $after) {
        $type_array = &self::$_stacks[$type];
        if (!isset($type_array[$stack])) {
            $type_array[$stack] = array();
        }

        if (!is_null($after)) {
            $new_array = array();
            //search array for $after key and insert our object after it
            $found = false;
            foreach ($type_array[$stack] as $key => $item) {
                $new_array[$key] = $item;
                if ($key == $after) {
                    $new_array[$name] = $obj;
                    $found = true;
                }
            }
            if (!$found) {
                $new_array[$name] = $obj;
            }
            $type_array[$stack] = $new_array;
        } else {
            $type_array[$stack][$name] = $obj;
        }
    }

    //singleton... static only
    final private function __construct(){}
}
