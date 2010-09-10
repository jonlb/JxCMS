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

css:
 - admin

provides: [admin]

...
 */

var $request = new Jx.Request();
var $content = null;
var $noticeArea = null;

window.addEvent('domready',function(){
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
    var toolbarContainer = document.id($content.tabBar).getParent('.jxBarContainer').retrieve('jxBarContainer');
    //add tabmenu plugin
    var tabmenu = new Jx.Plugin.ToolbarContainer.TabMenu();
    tabmenu.attach(toolbarContainer);

    //eventually we should get the dashboard here...
    //for now open an empty tab
    $content.add(
        new Jx.Tab({
            active: true,
            label: 'Dashboard',
            content: '<p>The dashboard will be here eventually.</p>'
        })
    );

    thePage.resize();

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

    $('page-container').fade('in');
});
