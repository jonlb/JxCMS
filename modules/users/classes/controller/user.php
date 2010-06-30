<?php

class Controller_User extends Controller_Site {

    public $_login_form = array(
        'class' => 'jxForm',
        'action' => '/user/login',
        
    );

    public function before(){
        parent::before();

    }

    public function action_login() {
        
    }

    public function action_logout() {

    }
}
