<?php

/**
 * This is the controller to use for all admin controllers.
 */
class Controller_Admin extends Controller_Site {


    public $template = 'admin';

    /**
     * Everything in an admin controller should require login. If you override
     * this in other controllers be sure to specify the 'allow_login' capability
     * or you will open up any action that doesn't have additional needed capabilities.
     */
    protected $security_all = array('allow_login');

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
