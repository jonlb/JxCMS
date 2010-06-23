<?php  defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Index extends Controller_Admin
{

    protected $security_action = array(
        'index' => 'allow_login'
    );
    
    public function action_index()
    {
        $this->template->message = 'Hello, Administrator!';
    }


}


