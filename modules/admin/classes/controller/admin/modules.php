<?php  defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Modules extends Controller_Admin {

    public function action_progress(){

    }

    public function action_uploadProgressID() {

    }

    public function action_upload() {

    }

    public function action_all() {

    }

    public function action_update() {

    }

    public function action_waiting() {
        
    }

    public function action_listAllPerm() {
        $this->template->modules = Jx_Modules::getPermanent();
    }
}
