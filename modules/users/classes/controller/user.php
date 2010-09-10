<?php

class Controller_User extends Controller_Site {

    public function before(){
        parent::before();

    }

    public function action_login() {

        if ($this->auth->logged_in('login')) {
            Request::instance()->redirect('/');
        }

        $form = new Jx_Form('login');

        Jx_Debug::dump($_POST);
        
        if (isset($_POST['login']) && $form->populate($_POST)) {
            $ret = $form->result();
            $user = $ret['models']['user'];
            $redirect = Session::instance()->get('redirect', null);
            if ($this->auth->login($user->username,$user->password, FALSE)) {
                if (!empty($redirect) && isset($redirect['fromUrl'])) {
                    Session::instance()->delete('redirect');
                    Request::instance()->redirect($redirect['fromUrl']);
                }
            }
        }
        $this->template->login_form = $form->render();


    }

    public function action_logout() {
        $this->auth->logout();
        Request::instance()->redirect('/');
    }
}
