<?php


class Jx_Acl {

    /**
     * Use this function to check whether a user has a capability either directly
     * or via a role he/she is assigned to. If an array is passed in then the user
     * must have ALL of the capabilities listed to pass this check.
     * @static
     * @param  $cap The capability to check for
     * @param  $user The user to check on (will grab pertinent roles as well)
     * @return void
     */
    public static function check_for_cap($cap, $user = null) {

        //if no user is passed in then check the default role
        if (!is_array($cap)) {
            $cap = array($cap);
        }

        if (empty($user)) {
            $default_role = Kohana::config('acl.default_role');
            foreach ($cap as $capability) {
                if (!self::check_role_for_cap($capability, $default_role)) {
                    return FALSE;
                }
            }
            return TRUE;
        } else {

            $result = TRUE;

            //first check the user directly
            foreach ($cap as $capability) {
                if (!self::check_user_for_cap($capability, $user)) {
                    $result = FALSE;
                }
            }

            if (TRUE === $result) {
                return TRUE;
            }

            //Jx_Debug::dump(null, 'not on user');
            $result = TRUE;
            //didn't find it on the user so check their roles
            foreach ($user->roles as $role) {
                if (!self::check_role_for_cap($cap,$role)) {
                    $result = FALSE;
                }
            }

            //Jx_Debug::dump($result, 'result of check on roles');
            return $result;
        }

    }

    private static function check_role_for_cap($cap, $role) {
        if (is_string($role)) {
            $role = Jelly::select('role')->where('name','=',$role)->load();
        }
        foreach ($role->capabilities as $capability) {
            if (in_array($capability->capability,$cap)) {
                return TRUE;
            }
        }
        return FALSE;
    }


    private static function check_user_for_cap($cap, $user) {
        foreach ($user->capabilities as $capability) {
            if (in_array($capability->capability,$cap)) {
                return TRUE;
            }
        }
        return FALSE;
    }

    public static function get_login_cap() {
        return Kohana::config('acl.login_cap');
    }

    public static function onGetAdminMenu(Jx_Event_Notification $notification) {
        if ($notification->hasReturnData()) {
            $menu = $notification->getReturnData();
        } else {
            $menu = $notification->getObject();
        }


        $menu['security'] = array(
            'text' => 'Security',
            'title' => '',
            'toplevel' => true,
            'submenu' => array(
                'groups' => array(
                    'text' => 'Manage Groups',
                    'title' => '',
                    'file' => 'groups'
                ),
                'users' => array(
                   'text' => 'Manage Users',
                   'title' => '',
                   'file' => 'users'
                )
            )
        );
        
        $notification->setReturnData($menu);
    }

}
