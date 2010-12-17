<?php


class Jx_View_Json {

    private $_data;

	public function __construct($request = null){
        $this->_data = new stdClass();
        if (is_a($request, 'Request')) {
            $request->headers['Content-Type'] = 'application/json';
            $id = $request->param('requestId',null);
            Jx_Debug::log($request,'Request variable','dump');
            if (!is_null($id)) {
                $this->requestId = $id;
                Jx_Debug::log($id, 'Passed in request ID');
            }
        }
	}

	public function __set($key, $value) {
		if ('_' == substr($key, 0, 1)) {
            throw new Jx_View_Exception('Setting private or protected class members is not allowed', $this);
        }
		$this->_data->$key = $value;
	}

	public function __get($key) {
		if ('_' == substr($key, 0, 1)) {
            throw new Jx_View_Exception('Getting private or protected class members is not allowed', $this);
        }
		return $this->_data->$key;
	}

	public function __isset($key) {
		if ('_' == substr($key, 0, 1)) {
            throw new Jx_View_Exception('checking private or protected class members is not allowed', $this);
        }
		return isset($this->_data->$key);
	}

	public function __unset($key) {
		if ('_' == substr($key, 0, 1)) {
            throw new Jx_View_Exception('Unsetting private or protected class members is not allowed', $this);
        }
		unset($this->_data->$key);
	}

	public function assign($spec, $value = null) {
		 // which strategy to use?
        if (is_string($spec)) {
            // assign by name and value
            if ('_' == substr($spec, 0, 1)) {
                throw new Jx_View_Exception('Setting private or protected class members is not allowed', $this);
            }
            $this->_data->$spec = $value;
        } elseif (is_array($spec)) {
            // assign from associative array
            $error = false;
            foreach ($spec as $key => $val) {
                if ('_' == substr($key, 0, 1)) {
                    $error = true;
                    break;
                }
                $this->_data->$key = $val;
            }
            if ($error) {
                throw new Jx_View_Exception('Setting private or protected class members is not allowed', $this);
            }
        } else {
            throw new Jx_View_Exception('assign() expects a string or array, received ' . gettype($spec), $this);
        }

        return $this;
	}

	public function clearVars() {
		$this->_data = null;
	}

	public function render() {
	   return json_encode($this->_data);

	}

    public function __toString() {
        return (string) $this->render();
    }

}
