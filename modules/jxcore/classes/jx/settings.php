<?php


class Jx_Settings {

    public static function get($key) {
        $setting = Jelly::select('setting')->where('setting','=',$key)->limit(1)->execute();
        if ($setting->loaded()) {
            return $setting->value;
        }
        return null;
    }

    public static function set($key, $value, $description = '') {
        $setting = Jelly::select('setting')->where('setting','=',$key)->limit(1)->execute();
        if (!$setting->loaded()) {
            Jelly::factory('setting')
                ->set(array(
                    'setting' => $key,
                    'value' => $value,
                    'description' => $description
            ))->save();
        } else {
            $setting->value = $value;
            $setting->save();
        }
    }

    public static function onGetAdminMenu(Jx_Event_Notification $notification) {
        if ($notification->hasReturnData()) {
            $menu = $notification->getReturnData();
        } else {
            $menu = $notification->getObject();
        }


        $menu['system']['submenu']['settings'] = array(
            'text' => 'Manage Settings',
            'title' => '',
            'file' => 'settings'
        );

        $notification->setReturnData($menu);
    }

}
