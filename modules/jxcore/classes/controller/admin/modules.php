<?php  defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Modules extends Controller_Admin {

    public function action_progress(){

    }

    public function action_uploadProgressID() {

    }

    public function action_upload() {

    }

    /**
     * Gets a listing of all currently installed modules and whether they are active or not
     * @return void
     */
    public function action_all() {
        $modules = Jx_Modules::get_all();
        $data = array();
        foreach ($modules as $name => $values) {
            $obj = new stdClass();
            $obj->name = ucfirst($name);
            $obj->id = $values['id'];
            $obj->active = Jx_Modules::isActivated($name);
            $obj->permanent = Jx_Modules::isPermanent($name);
            $obj->version = Jx_Modules::getVersion($name);
            $data[] = $obj;
        }
        $this->template->data = $data;
        $this->template->success = true;

    }

    public function action_update() {

    }

    public function action_waiting() {
        
    }

    public function action_listAllPlugins() {
        $this->template->modules = Kohana::config('plugins');
        $this->template->success = true;
    }
}
