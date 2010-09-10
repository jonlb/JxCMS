<?php


Jx_Event::addObserver(array('Jx_Theme','onBeforeRender'),'beforeRender');
Jx_Event::addObserver(array('Jx_Theme', 'onGetAdminMenu'), 'getAdminMenu');
