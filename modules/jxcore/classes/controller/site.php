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

        $this->auth = Auth::instance();
        $user = $this->auth->get_user();

        //Jx_Debug::dump($this->auth,'auth object');
        //Jx_Debug::dump($user, 'User object');
        $capabilities = FALSE;
        //first check $this->security_all
        if ($this->security_all !== FALSE) {
            if (is_array($this->security_all)){
                $capabilities = $this->security_all;
            } else {
                $capabilities = array($this->security_all);
            }
        }

        if ($this->security_action !== FALSE && array_key_exists($this->request->action,$this->security_action)) {
            $c = $this->security_action[$this->request->action];

            if (is_array($c)) {
                $capabilities = array_merge($capabilities, $c);
            } else {
                $capabilities[] = $c;
            }
        }

        //Jx_Debug::dump($capabilities, 'checking for capabilities');
        if (FALSE !== $capabilities) {
            if (in_array(Jx_Acl::get_login_cap(), $capabilities) && !$this->auth->logged_in()) {
                //Jx_Debug::dump(null,'not logged in');
                Session::instance()->set('redirect', array('fromUrl' => $this->request->uri));
                $this->request->redirect(Route::get('users')->uri(array('action'=>'login')));
            } else if (!Jx_Acl::check_for_cap($capabilities, $user)) {
                //Jx_Debug::dump(null, 'no capability');
                Session::instance()->set('redirect', array('fromUrl' => $this->request->uri));
                //$this->request->redirect(Route::get('users')->uri(array('action'=>'denied')));
            }
        }

        //Jx_Debug::dump(null, 'checks passed');

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
