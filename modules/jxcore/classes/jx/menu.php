<?php


class Jx_Menu {

    public static function get_admin_menu() {
        /**
         * each menu item should be setup as a button config with an extra parameter
         * for the filename that should be loaded to render the panel/tab
         */
        $menu = array(
            'content' => array(
                'toplevel' => true,
                'text' => 'Site Content',
                'title' => '',
                'submenu' => array()
            ),
            'system' => array(
                'toplevel' => true,
                'text' => 'System',
                'title' => '',
                'submenu' => array(
                    'dashboard' => array(
                        'text' => 'Dashboard',
                        'title' => '',
                        'file' => 'dashboard'
                    )
                )
            ),
            'plugins' => array(
                'toplevel' => true,
                'text' => 'Plugins',
                'title' => '',
                'submenu' => array()
            )
        );

        $notification = Jx_Event::post($menu, 'getAdminMenu');

        $menu = $notification->getReturnData();

        //sort the submenus
        foreach ($menu as $key => $arr) {
            array_multisort($menu[$key]['submenu'], SORT_ASC);
        }

        return $menu;
    }
}
