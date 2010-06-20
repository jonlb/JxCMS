<?php

/**
 * This is the controller to use for all admin controllers.
 */
class Controller_Admin extends Controller_Site {


    public $template = 'admin';
    
    protected $auth;

    /**
     * The before() function will be used to check Auth and ACL
     * to ensure we're allowed in here....
     *
     * @return void
     */
    public function before() {

        
        //if all passes, call the parent before()
        parent::before();
    }


    public function after() {
        parent::after();
    }
}
