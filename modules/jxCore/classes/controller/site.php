<?php


class Controller_Site extends Controller {

    public $template;

    public $auto_render = true;
    protected $auth;

    /**
     * This will specify a blanket capability(ies) needed to access everything
     * in this controller. Especially handy for specifying the 'allow_login'
     * capability for admin controllers.
     */
    protected $security_all = FALSE;

    /**
     * This variable will hold the security settings for a controller.
     * Specifically, it will list the capabilities needed to access each
     * action in a controller. If an action isn't listed it will be given
     * full access to everyone. Should look like:
     *
     * protected $security_action = array(
     *      'action1' => array('capability1','capability2'),
     *      'action2' => array('capability2','capability3')
     * );
     *
     * You should try to use a currently existing capability if possible.
     * Anything with a capability also requires the user to login.
     */
    protected $security_action = FALSE;

    public function before() {

        if ($security !== FALSE && array_key_exists($this->request->action,$this->security)) {
            //check the security here...
            $this->auth = Auth::instance();
            if (!$this->auth->logged_in()) {
                $this->request->redirect(Route::get('users')->uri(array('action'=>'login')));
            }

        }




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
