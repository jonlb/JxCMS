<?php


interface Jx_Module_Interface {

    public static function getVersion();
    public static function install();
    public static function uninstall();
    public static function activate();
    public static function deactivate();

}
 
