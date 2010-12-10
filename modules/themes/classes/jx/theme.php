<?php

class Jx_Theme {

    /**
     * This function will determine the appropriate theme and set it as the base template.
     * @static
     * @param  $notification
     * @return void
     */
    public static function onBeforeRender(Jx_Event_Notification $notification) {

        $controller = $notification->getObject();

        $format = $controller->request->param('format','html');

        if ($format !== 'html' || is_a($controller, 'Controller_Admin')) {
            return;
        }
        
        $template = $notification->getOptions();

        //eventually we will get the proper theme set in the database...
        //for now, just change the next line

        //check referer...
        $base = '';
        $redirect = (array) Session::instance()->get('redirect');
        if (isset($redirect['fromUrl']) && strpos($redirect['fromUrl'],'admin') !== FALSE) {
            $base = 'admin'.DS;
        } else {
            $base = self::get_current_theme_dir().DS;
        }

        $template->base_layout = $base.'base.html';

    }

    public static function get_current_theme_dir(){
        //Eventually we should check the database for the current theme
        //for right now, just return one
        return Jx_Settings::get('theme.activeDirectory');
    }


    public static function activate(){

    }

    public static function deactivate(){

    }

    public static function onGetAdminMenu(Jx_Event_Notification $notification){

        if ($notification->hasReturnData()) {
            $menu = $notification->getReturnData();
        } else {
            $menu = $notification->getObject();
        }


        $menu['system']['submenu']['theme'] = array(
            'text' => 'Manage Themes',
            'title' => '',
            'file' => 'theme'
        );

        $notification->setReturnData($menu);

        
    }

}
