<?php

class Jx_Theme {

    /**
     * This function will determine the appropriate theme and set it as the base template.
     * @static
     * @param  $notification
     * @return void
     */
    public static function onBeforeRender(Jx_Event_Notification $notification) {

        $format = $notification->getObject()->request->param('format','html');

        if ($format !== 'html') {
            return;
        }
        
        $template = $notification->getOptions();

        //eventually we will get the proper theme set in the database...
        //for now, just change the next line

        $template->base_layout = 'theme2/base.html';

    }

}
