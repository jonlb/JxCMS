<?php


class Jx_Form {

    /**
     * a map of Jelly fields to form elements
     */
    public static $field_map = array(
        'Field_Boolean' => 'checkbox',
        'Field_Email' => 'text',
        'Field_Enum' => 'select',
        'Field_File' => 'file',
        'Field_Float' => 'text',
        'Field_Integer' => 'text',
        'Field_Password' => 'password',
        'Field_Slug' => 'text',
        'Field_String' => 'text',
        'Field_Text' => 'textarea',
        'Field_Timestamp' => 'text'
    );

    public $model;

    protected $metas = array();

	public $is_valid = true;

	protected $progress_id;

	protected $edit_mode;

    protected $validator;

    protected $model_ids;

	public function __construct($file = null, $auto_init = TRUE) {
		if ($file != null) {
			$this->model = require Kohana::find_file('forms', $file);
		}
		$this->config = Kohana::config('form');
        $this->init();
	}

	protected function init() {
		foreach ($this->model['fields'] as $key => &$field) {

			if (array_key_exists('data_source', $field)) {
				$val_field = $field['data_source']['val_field'];
				$text_field = $field['data_source']['text_field'];
				$params = array_key_exists('params', $field['data_source']) ? $field['data_source']['params'] : null;
				$list = call_user_func_array($field['data_source']['callback'], $params);

				if ( ! array_key_exists('items', $field)) {
					$field['items'] = array();
				}

				if ( ! array_key_exists('result', $field['data_source']) || $field['data_source']['result'] == 'array') {
					foreach ($list as $item) {
						$field['items'] []= array('value' => $item[$val_field], 'text' => $item[$text_field]);
					}
				} else if ($field['data_source']['result'] == 'object') {
					foreach ($list as $item) {
						$field['items'] []= array('value' => $item->$val_field, 'text' => $item->$text_field);
					}
				} else {
					throw new Exception('unknown result type: '.$field['data_source']['result']);
				}
			}

            //pull info if from a Jelly Model
            if (array_key_exists('model',$field)) {
                //get model
                if (!isset($this->models[$field['model']])) {
                    $this->metas[$field['model']] = $meta = Jelly::meta($field['model']);
                } else {
                    $meta = $this->metas[$field['model']];
                }
                $fld = $meta->fields($field['field']);

                //get Jelly class of field
                $class = get_class($fld);
                $field['type'] = self::$field_map[$class];

                //now grab info needed from the field
                $field['name'] = $fld->name;
                if ($this->model['populate_defaults'] === true) {
                    $field['value'] = $fld->default;
                }
            }
            if (isset($field['attributes'])) {
                $field['attributes'] = array();
            }
            $field['attributes']['type'] = $field['type'];

            if (in_array($field['type'],array('password'))) {
                $field['type'] = 'text';
            }
            //Jx_Debug::dump($field, "field $key in init()");
		}
	}

    /**
     * @param  array $src if $from_model == true then $src is an assoc array of model names to PK values, else
     *              its an array of field names/values
     * @param bool $from_model indicates whether we should pull data from
     *              models where appropriate, from $src array otherwise.
     * @return void
     */
	public function pre_populate(array $src, $from_model = false) {
		$this->edit_mode = true;
		$progress_id = null;

        if ($from_model) {
            $this->model_ids = $src;
            //load the models
            foreach ($this->model as $key => $field) {
                if (isset($field['model']) && isset($src[$field['model']])) {
                    $model = Jelly::select($field['model'], $src[$field['model']]);
                    if ($model->loaded()) {
                        $field['value'] = $model->{$field['field']};
                    }
                } else {
                    if (isset($field['name']) && isset($src[$field['name']])) {
                        $field['value'] = $src[$field['name']];
                    }
                }
            }
        } else {
            foreach ($src as $prop => $val) {
                $found = false;
                foreach ($this->model['fields'] as &$field) {
                    if (array_key_exists('name', $field) &&
                    $field['name'] == $prop) {
                        $found = true;
                        $field['value'] = $val;
                    }
                }
                if ( ! $found) {
                    if ($progress_id == null) {
                        $progress_id = $this->create_progress_id();
                    }
                    $_SESSION[$this->config['session_key']]['progress'][$progress_id][$prop] = $val;
                }
            }
        }
	}

	public function populate($src) {
        if (empty($src)) {
            $src = $_POST;
        }
        $this->data = $src;
        //validate data first...
        $this->validate();

        //if that failed then populate it into the fields for rendering
		foreach ($this->model['fields'] as &$field) {
			if (array_key_exists('name', $field) &&
			array_key_exists($field['name'], $src)) {
				if ($src[$field['name']] === "" && array_key_exists('on_empty', $field)) {
					$field['value'] = $field['on_empty'];
				} else {
					$field['value'] = $src[$field['name']];
				}
			}
		}
		if (array_key_exists($this->config['progress_key'], $src)) {
			$this->progress_id = $src[$this->config['progress_key']];
			$this->edit_mode = true;
		}
		return $this->is_valid;
	}

	protected function create_progress_id() {
		$sess_key = $this->config['session_key'];
		if ( ! array_key_exists($sess_key, $_SESSION)) {
			$_SESSION[$sess_key] = array();
		}
		if ( ! array_key_exists('progress', $_SESSION[$sess_key])) {
			$_SESSION[$sess_key]['progress'] = array();
		}

		if ( ! array_key_exists('progress_counter', $_SESSION[$sess_key])) {
			$_SESSION[$sess_key]['progress_counter'] = 0;
		}
		if ($_SESSION[$sess_key]['progress_counter'] == 32767) {
			$_SESSION[$sess_key]['progress_counter'] = 0;
		}

		$progress_id = sha1($_SESSION[$sess_key]['progress_counter']++);
		$_SESSION[$sess_key]['progress'][$progress_id] = array();

		$this->model['fields'] []= array(
			'name' => $this->config['progress_key'],
			'type' => 'hidden',
			'value' => $progress_id
		);

		return $progress_id;
	}

	public function validate() {
        //setup the validator
        $this->validator = $validator = new Validate($this->data);

        Jx_Debug::dump($this->data, 'The data to validate');
        foreach ($this->model['fields'] as &$field) {
            if (isset($field['hide_on_edit']) && $field['hide_on_edit'] === true && $this->edit_mode) {
			    continue;
            }
            //first, check on the validation from the model
            if ($this->model['use_model_validation'] && array_key_exists('model',$field)) {
                $model = $this->metas[$field['model']];
                //Jx_Debug::dump($model, "The {$field['model']} model");
                //add rules, filters, and callbacks
                $fld = $model->fields($field['name']);
                Jx_Debug::dump($fld, 'the field object');
                if (isset($fld->rules)) {
                    $validator->rules($field['name'],$fld->rules);
                }
                if (isset($fld->callbacks)){
                    $validator->callbacks($field['name'], $fld->callbacks);
                }
                if (isset($fld->filters)) {
                    $validator->filters($field['name'],$fld->filters);
                }
            } elseif (isset($field['validation'])) {
                //check if the form model has field validators...
                if (isset($field['validation']['rules'])) {
                    $validator->rules($field['name'],$field['validation']['rules']);
                }
                if (isset($field['validation']['callbacks'])){
                    $validator->callbacks($field['name'], $field['validation']['callbacks']);
                }
                if (isset($field['validation']['filters'])) {
                    $validator->filters($field['name'],$field['validation']['filters']);
                }
            }
            //now add anything setup in the field itself...
			if (array_key_exists('validation', $field)) {
				if (isset($field['validation']['rules'])) {
                    $validator->rules($field['name'],$field['validation']['rules']);
                }
                if (isset($field['validation']['callbacks'])){
                    $validator->callbacks($field['name'], $field['validation']['callbacks']);
                }
                if (isset($field['validation']['filters'])) {
                    $validator->filters($field['name'],$field['validation']['filters']);
                }
			}
		}
        //add global validators...
        if (array_key_exists('validation', $this->model)) {
            if (isset($this->model['validation']['rules'])) {
                foreach ($this->model['validation']['rules'] as $rule => $params) {
                    $validator->rule(TRUE,$rule, $params);
                }
            }
            if (isset($this->model['validation']['filters'])) {
                foreach ($this->model['validation']['filters'] as $filter => $params) {
                    $validator->filter(TRUE,$filter, $params);
                }
            }
        }

        Jx_Debug::dump($validator,'the validator object');

		if ($validator->check()) {
            Jx_Debug::dump(null, 'Check passed');
            $this->is_valid = true;
        } else {
            Jx_Debug::dump(null,'Check failed');
            $this->is_valid = false;
            $this->errors = $validator->errors($this->model['messages']);
            Jx_Debug::dump($this->errors, 'The errors were');
        }
        return $this->is_valid;
	}

	public function result() {
		$result_type = $this->model['result_type'];
		if ($result_type == 'array') {
			$rval = array_intersect_key($this->validator->as_array(), $this->data);
		} elseif ($result_type == 'model') {
            //go through each field, create an instance of the model and
            //populate data
            $rval = array(
                'models' => array(),
                'other' => array()
            );
            foreach ($this->model['fields'] as $field) {
                if (array_key_exists('model', $field)) {
                    if (!isset($rval['models'][$field['model']])) {
                        if (isset($this->model_ids[$field['model']])) {
                            $model = $rval['models'][$field['model']] = Jelly::select($field['model'], $this->model_id[$field['model']]);
                        } else {
                            $model = $rval['models'][$field['model']] = Jelly::factory($field['model']);
                        }

                    } else {
                        $model = $rval['models'][$field['model']];
                    }
                    $model->set($field['field'],$this->validator[$field['field']]);
                } else {
                    if (isset($this->validator[$field['name']])){
                        $rval['other'][$field['name']] = $this->validator[$field['name']];
                    } elseif (isset($this->data[$field['name']])) {
                        $rval['other'][$field['name']] = $this->data[$field['name']];
                    }
                }
            }
        } else {
			$rval = new $result_type;
			foreach ($this->model['fields'] as $field) {
				if (array_key_exists('name', $field) && array_key_exists('value', $field)) {
					$prop = $field['name'];
					$rval->$prop = $field['value'];
				}
			}
			if ($this->progress_id != null) {
				foreach ($_SESSION[$this->config['session_key']]['progress'][$this->progress_id] as $k => $v) {
					$rval->$k =$v;
				}
			}
		}
        return $rval;
	}

	protected function get_error(array $field) {
		return $this->errors[$field];
	}

	protected function load_defaults() {
		if ( ! array_key_exists('view', $this->model)) {
			$this->model['view'] = 'form/layout';
		}
		if ( ! array_key_exists('attributes', $this->model)) {
			$this->model['attributes'] = array();
		}
		if ( ! array_key_exists('method', $this->model['attributes'])) {
			$this->model['attributes']['method'] = 'post';
		}
		if ( ! array_key_exists('action', $this->model['attributes'])) {
			$this->model['attributes']['action'] = '';
		}

		if ( ! array_key_exists('autoadd_asterisk', $this->model)) {
			$this->model['autoadd_asterisk'] = true;
		}
		$delete_keys = array();
		foreach ($this->model['fields'] as $k => &$field) {
            //Jx_Debug::dump($field, 'field');
            if (!isset($field['attributes'])) {
                $field['attributes'] = array();
            }
			if (isset($field['hide_on_edit']) && $field['hide_on_edit'] === true && $this->edit_mode) {
				$field['type'] = 'hidden';
				continue;
			}
			if (array_key_exists('label_key', $field)) {
				if (is_array($field['label_key'])) {
					$field['label'] = Kohana::message($this->model['label_file'], $field['label_key'][$this->edit_mode ? 'on_edit' : 'on_create']);
				} else {
					$field['label'] = Kohana::message($this->model['label_file'], $field['label_key']);
				}
			}

			if (array_key_exists('label', $field) && is_array($field['label'])) {
				$field['label'] = $field['label'][$this->edit_mode ? 'on_edit' : 'on_create'];
			}

			if (array_key_exists('validation', $field)) {
				foreach ($field['validation'] as $validator) {
					if (array_key_exists('not_empty', $validator)
					&& $this->model['autoadd_asterisk'] === true) {
						$field['not_empty'] = true;
					}
				}
			}

			if ( ! array_key_exists('view', $field)) {
				$field['view'] = 'form/'.$field['type'];
			}
			$v = Jx_View::factory($field['view']);
			$v->model = $field;
            $field['view'] = $v->render();
            
		}
		
		//print_r($this->model); die();
	}

	public function render() {
		$this->load_defaults();
		$view = Jx_View::factory($this->model['view']);
		$view->model = $this->model;
        if (isset($this->erros)) {
            $view->errors = $this->errors;
        }
		return $view->render();
	}

	public function __toString() {
		return $this->render();
	}

}
