<?php

class Jx_Debug {

    public static function dump($variable, $label = NULL) {
        if (isset($label)) {
            echo $label . ":";
        }
        echo "<br><pre>";
        var_dump($variable);
        echo "</pre><br>";
    }
}