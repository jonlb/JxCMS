<?php


class Jx_View {

    public static function factory($controller, $format = null) {
        if (is_a($controller,'Controller_Site')) {
            $format = $controller->request->param('format','html');
            switch ($format) {
                case 'html':
                    $template;
                    if (empty($controller->template)) {
                        // Generate a template name if one wasn't set.
                        $template = str_replace('_', '/', $controller->request->controller).'/'.$controller->request->action;
                    } else {
                        $template = $controller->template;
                    }

                    if ($controller->auto_render) {
                        // Load the twig template.
                        $template = Twig::factory($template, 'default');

                        // Return the twig environment
                        $controller->environment = $template->environment();
                    }
                    return $template;
                    break;
                case 'json':
                    return new Jx_View_Json($controller->request);
                    break;
                case 'rss':

                    break;
                case 'xml':

                    break;
            }
        } elseif (is_string($controller)) {
            if (is_null($format)) {
                $format = 'html';
            }
            switch ($format) {
                case 'html':
                    $template = $controller;
                    return Twig::factory($template, 'default');
                break;
                case 'json':
                    if (is_a($controller, 'Request')) {
                        return new Jx_View_Json($controller);
                    } else {
                        return new Jx_View_Json();
                    }
                break;
            }
        }


    }
}
