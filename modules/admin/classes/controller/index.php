<?php  defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Index extends Controller_Template
{
    public $template = 'admin';

    function action_index()
    {
        $this->template->message = 'Hello, Administrator!';
    }
}


