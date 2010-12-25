/*
---

name: admin

description:

license: MIT-style license.

requires:
 - menuSystem
 - jxlib/Jx.Layout
 - jxlib/Jx.Notifier.Float
 - jxlib/Jx.TabBox
 - jxlib/Jx.Plugin.ToolbarContainer.TabMenu
 - core/JSON
 - Jx.Manager.Module

css:
 - admin

provides: [admin]

...
 */

var $request = new Jx.Request();
var $content = null;
var $noticeArea = null;
var $moduleManager = new Jx.Manager.Module({
    type: 'module',
    request: $request,
    url: '/admin/modules/listAllPlugins.json'
});

window.addEvent('domready',function(){

    $moduleManager.getAllPlugins();

    //set layouts
    var thePage = new Jx.Layout('page-container');
    new Jx.Layout('menubar',{
        height: 70
    });

    new Jx.Layout('footer', {
        top: null,
        height: 25
    });

    var contentArea = new Jx.Layout('page',{
        top: 70,
        bottom: 25
    });


    //add Tabbox to contentArea
    $content = new Jx.TabBox({parent: 'page'});

    thePage.resize();

    var toolbarContainer = document.id($content.tabBar).getParent('.jxBarContainer').retrieve('jxBarContainer');
    //add tabmenu plugin
    var tabmenu = new Jx.Plugin.ToolbarContainer.TabMenu();
    tabmenu.attach(toolbarContainer);



    $noticeArea = new Jx.Notifier.Float({
        parent: 'menubar',
        position: {
            horizontal: 'right right',
            vertical: 'top top'
        }
    });

    $($noticeArea).setStyles({
        bottom: 'auto',
        left: 'auto',
        top: 70,
        right: 0,
        'z-index': 65000
    });

    var menuSystem = new MenuSystem(menu);

    $content.tabSet.addEvent('tabChange', function(tabset, tab){
        debugger;
        tab.module.createInterface();
    });

    //register with moduleManager to learn about new tabs added and show them
    $moduleManager.addEvent('itemAdded', function(el){
        $content.add(el.tab);

    });

    $('page-container').fade('in');

    //eventually we should get the dashboard here...

});
