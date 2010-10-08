<?php  defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Index extends Controller_Admin {

    public function action_index()
    {
        $loader_uri = Jx_Loader::uri(array(
            'file' => array('loader'),
            'page' => 'admin',
            'clearSession' => 'true',
            'rebuild' => 'true',
            'compress' => 'false'
        ));

        Jx_Assets::add_js_file('options','main','options');
        Jx_Assets::add_js_file($loader_uri,'main','loader','options');
        //add the menu
        $menu = Jx_Menu::get_admin_menu();
        $menu = json_encode($menu);
        $menu_script = <<< MENU
var menu = $menu;
MENU;
        Jx_Assets::add_js_script($menu_script,'main','menu','loader');

        $loader_uri = Jx_Loader::uri(array('file'=>array('admin')));
        $script = <<< 'SCRIPT'
$uses(['admin'],'admin',null,function(){});
SCRIPT;

        Jx_Assets::add_js_script($script,'main','admin','menu');

        //styles... hide the page
        $styles = <<< styles
#page-container {
    visibility: hidden;
    opacity: 0;
}
styles;

        Jx_Assets::add_css_styles($styles, 'main', 'hide_page');

        //other things needed
        $this->template->username = $this->auth->get_user()->username;
        $this->template->logout_link = Route::get('users')->uri(array('action'=>'logout'));

    }


}


