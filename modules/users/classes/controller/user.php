<?php

class Controller_User extends Controller_Site {

    public function before(){
        parent::before();

    }

    public function action_login() {
        $user = Jelly::factory('user');

        $user->set(Arr::extract($_POST, array('username', 'password')));

        $user->subform(array('username', 'password'))
                ->add('submit','submit');

        $user->form->set('view_prefix','formo');
        
        $this->template->login_form = $user->subform->render('html');

        $user->subform->load();

        //Jx_Debug::dump($user->subform);
        //die();

        if ($user->subform->validate()) {
            Jx_Debug::dump('data validated...');
            if ($user->login()) {
                $this->request->redirect('admin');
            }

            $user->subform->error('invalid_login');
        }
    }

    public function action_logout() {

    }
}
