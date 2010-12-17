<?php

class Request extends Kohana_Request {

    public function get_param($key = null, $default = null) {

        if (empty($key)) {
            return $_REQUEST;
        }
        
        $ret = isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
        if (is_bool($default) && is_string($ret)) {
            if ($ret === 'true') {
                $ret = true;
            } else {
                $ret = false;
            }
        }
        //echo "<br>$key = "; var_dump($ret);
        return $ret;
    }

    public function __construct($uri){
        parent::__construct($uri);

        //merge all params into a single array.
        $this->_params = array_merge_recursive($this->_params, $_COOKIE, $_GET, $_POST, $_FILES, $_SERVER);
    }
}
