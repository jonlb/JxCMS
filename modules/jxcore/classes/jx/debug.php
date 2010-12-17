<?php

class Jx_Debug {

    public static function dump($variable, $label = NULL, $type = 'log') {

        if (class_exists('Fire')) {
            switch ($type) {
                case 'info':
                    Fire::info($variable, $label);
                    break;
                case 'log':
                    Fire::log($variable, $label);
                    break;
                case 'warn':
                    Fire::warn($variable, $label);
                    break;
                case 'error':
                    Fire::error($variable, $label);
                    break;
                case 'dump':
                    Fire::dump($variable, $label);
                    break;
            }
        } else {
            if (isset($label)) {
                echo $label . ":";
            }
            echo "<br><pre>";
            var_dump($variable);
            echo "</pre><br>";
        }
    }

    public static function log($variable, $label) {
        self::dump($variable, $label, 'log');
    }

    public static function info($variable, $label) {
        self::dump($variable, $label, 'info');
    }

    public static function warn($variable, $label) {
        self::dump($variable, $label, 'warn');
    }


    public static function error($variable, $label) {
        self::dump($variable, $label, 'error');
    }
}