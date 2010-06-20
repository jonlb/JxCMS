<?php


class Controller_Site extends Controller {

    public $template;

    public $auto_render = true;

    public function before() {

        $this->auth = Auth::instance();

        //check AUTH


        //check ACL

        if ($this->auto_render) {
            $this->template = Jx_View::factory($this);
        }

        return parent::before();
    }

    public function after() {

        if ($this->auto_render) {
            Jx_Event::post($this,'beforeRender',$this->template);

            $this->request->response = $this->template;
        }

        return parent::after();
    }
}
